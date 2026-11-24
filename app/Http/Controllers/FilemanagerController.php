<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class FilemanagerController extends Controller
{
    public function index(Request $request): View
    {
        $maxStorageAllowed = 50_000_000; // 50 MB limit

        $validated = $request->validate([
            'path' => ['nullable', 'string'],
            'search' => ['nullable', 'string'],
        ]);
        $path = $this->safePath($validated['path'] ?? '');
        $disk = Storage::disk('public');
        $totalStorageUsed = collect($disk->allFiles((string) auth()->id()))
            ->sum(fn (string $file): int => $disk->size($file));
        $directory = $this->fullPath($path);
        $search = trim($validated['search'] ?? '');

        $directories = collect($search === '' ? $disk->directories($directory) : $disk->allDirectories($directory))
            ->map(fn (string $directory) => [
                'name' => basename($directory),
                'path' => $this->relativePath($directory),
                'type' => 'directory',
            ]);

        $files = collect($search === '' ? $disk->files($directory) : $disk->allFiles($directory))
            ->map(fn (string $file) => [
                'name' => basename($file),
                'path' => $this->relativePath($file),
                'type' => 'file',
                'size' => $disk->size($file),
                'modified' => $disk->lastModified($file),
                'mime' => $disk->mimeType($file),
                'url' => $disk->url($file),
            ]);

        if ($search !== '') {
            $matches = fn (array $item) => Str::contains(Str::lower($item['name']), Str::lower($search));
            $directories = $directories->filter($matches)->values();
            $files = $files->filter($matches)->values();
        }

        $allowUpload = $totalStorageUsed < $maxStorageAllowed;

        return view('filemanager.index', [
            'path' => $path,
            'directories' => $directories,
            'files' => $files,
            'search' => $search,
            'allowUpload' => $allowUpload,
            'totalStorageUsed' => $totalStorageUsed,
            'maxStorageAllowed' => $maxStorageAllowed,
        ]);
    }

    public function upload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string'],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => [
                'file',
                'max:51200',
                function (string $attribute, UploadedFile $file, \Closure $fail): void {
                    $blockedExtensions = [
                        'asa', 'asp', 'aspx', 'bat', 'bash', 'cgi', 'cmd', 'com', 'dll', 'exe',
                        'fish', 'htm', 'html', 'jar', 'js', 'jsp', 'mjs', 'php', 'phtml',
                        'phar', 'pl', 'ps1', 'py', 'rb', 'sh', 'svg', 'vbs', 'war', 'wsf',
                    ];
                    $blockedMimeTypes = [
                        'application/javascript', 'application/x-httpd-php', 'application/x-msdownload',
                        'application/x-sh', 'text/html', 'image/svg+xml', 'text/javascript',
                    ];

                    if (in_array(strtolower($file->getClientOriginalExtension()), $blockedExtensions, true)
                        || in_array(strtolower((string) $file->getMimeType()), $blockedMimeTypes, true)) {
                        $fail(__('Executable or script files are not allowed.'));
                    }
                },
            ],
        ]);

        $directory = $this->safePath($validated['path'] ?? '');
        $disk = Storage::disk('public');
        $fullDirectory = $this->fullPath($directory);

        foreach ($request->file('files', []) as $file) {
            if ($disk->putFileAs($fullDirectory, $file, $this->uniqueFilename($disk, $fullDirectory, $file)) === false) {
                throw new RuntimeException(__('Unable to store the uploaded file.'));
            }
        }

        return to_route('filemanager.index', ['path' => $directory])->with('success', __('Files uploaded successfully.'));
    }

    public function createFolder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string'],
            'name' => [
                'required',
                'string',
                'max:100',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $folder = is_string($value) ? trim($value) : '';

                    if ($folder === '' || in_array($folder, ['.', '..'], true)
                        || str_contains($folder, '/') || str_contains($folder, '\\')) {
                        $fail(__('The folder name is invalid.'));
                    }
                },
            ],
        ]);

        $directory = $this->safePath($validated['path'] ?? '');
        $folder = trim($validated['name']);
        $target = $this->fullPath($directory === '' ? $folder : $directory.'/'.$folder);

        if (! Storage::disk('public')->makeDirectory($target)) {
            throw new RuntimeException(__('Unable to create the folder.'));
        }

        return to_route('filemanager.index', ['path' => $directory])->with('success', __('Folder created successfully.'));
    }

    public function download(Request $request)
    {
        $validated = $request->validate(['path' => ['required', 'string']]);
        $path = $this->safePath($validated['path']);
        $fullPath = $this->fullPath($path);
        abort_unless($path !== '' && Storage::disk('public')->exists($fullPath), 404);

        return Storage::disk('public')->download($fullPath);
    }

    public function delete(Request $request): RedirectResponse
    {
        $validated = $request->validate(['path' => ['required', 'string']]);
        $path = $this->safePath($validated['path']);
        $disk = Storage::disk('public');
        $fullPath = $this->fullPath($path);

        if ($disk->directoryExists($fullPath)) {
            $deleted = $disk->deleteDirectory($fullPath);
        } elseif ($disk->exists($fullPath)) {
            $deleted = $disk->delete($fullPath);
        } else {
            $deleted = true;
        }

        if (! $deleted) {
            throw new RuntimeException(__('Unable to delete the selected item.'));
        }

        $parent = dirname($path);
        $parent = $parent === '.' ? '' : $parent;

        return to_route('filemanager.index', ['path' => $parent])->with('success', __('Item deleted successfully.'));
    }

    private function safePath(?string $path): string
    {
        $path = trim(str_replace('\\', '/', (string) $path), '/');
        abort_if($path !== '' && collect(explode('/', $path))->contains(fn (string $part) => $part === '..' || $part === ''), 400);

        return $path;
    }

    private function fullPath(string $path): string
    {
        return (string) auth()->id().($path === '' ? '' : '/'.$path);
    }

    private function relativePath(string $path): string
    {
        return ltrim(Str::after($path, (string) auth()->id()), '/');
    }

    private function uniqueFilename($disk, string $directory, UploadedFile $file): string
    {
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $filename = $original.($extension ? '.'.$extension : '');
        $counter = 1;

        while ($disk->exists($directory === '' ? $filename : $directory.'/'.$filename)) {
            $filename = $original.'-'.$counter++.($extension ? '.'.$extension : '');
        }

        return $filename;
    }
}
