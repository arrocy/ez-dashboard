@extends('layouts.main')

@section('title', __('File Manager'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">@lang('File Manager')</h1>
        <p class="text-body-secondary mb-0">@lang('Storage size') {{ short_num($totalStorageUsed) }} @lang('of') {{ short_num($maxStorageAllowed) }} @lang('used')</p>
    </div>
</div>

<div class="d-grid gap-4">
    <div class="card border-light-subtle shadow-sm">
        <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <nav aria-label="{{ __('File path') }}">
                <ol class="breadcrumb mb-0 flex-wrap align-items-center">
                    <li class="breadcrumb-item">
                        <a href="{{ route('filemanager.index') }}">@lang('Files')</a>
                    </li>
                    @php $crumbPath = ''; @endphp
                    @foreach ($path === '' ? [] : explode('/', $path) as $crumb)
                        @php $crumbPath = $crumbPath === '' ? $crumb : $crumbPath.'/'.$crumb; @endphp
                        <li class="breadcrumb-item">
                            <a href="{{ route('filemanager.index', ['path' => $crumbPath]) }}">{{ $crumb }}</a>
                        </li>
                    @endforeach
                </ol>
            </nav>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                    <i class="fa-solid fa-folder-plus me-2" aria-hidden="true"></i>@lang('New folder')
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadFilesModal" {{ $allowUpload ? '' : 'disabled' }}>
                    <i class="fa-solid fa-cloud-arrow-up me-2" aria-hidden="true"></i>@lang('Upload files')
                </button>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('filemanager.index') }}" class="w-100" style="max-width: 28rem;">
        <input type="hidden" name="path" value="{{ $path }}">
        <label class="visually-hidden" for="file-search">@lang('Search files')</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
            <input
                id="file-search"
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="{{ __('Search files...') }}"
                class="form-control"
            >
        </div>
    </form>

    <div class="card border-light-subtle shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead>
                    <tr>
                        <th scope="col">@lang('Name')</th>
                        <th scope="col">@lang('Type')</th>
                        <th scope="col">@lang('Modified')</th>
                        <th scope="col">@lang('Size')</th>
                        <th scope="col" class="text-end">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($directories as $directory)
                        <tr>
                            <td>
                                <a href="{{ route('filemanager.index', ['path' => $directory['path']]) }}" class="d-inline-flex align-items-center gap-3 fw-medium text-body">
                                    <span class="d-flex align-items-center gap-3">
                                        <i class="fa-solid fa-folder fa-2xl" aria-hidden="true"></i>
                                    </span>
                                    <span class="text-break">{{ $directory['name'] }}</span>
                                    @if ($search && dirname($directory['path']) !== ($path ?: '.'))
                                        <small class="text-body-secondary">{{ dirname($directory['path']) }}</small>
                                    @endif
                                </a>
                            </td>
                            <td class="small text-body-secondary">@lang('Folder')</td>
                            <td class="small text-body-secondary">—</td>
                            <td class="small text-body-secondary">—</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('filemanager.delete') }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="path" value="{{ $directory['path'] }}">
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="{{ __('Delete folder') }}"
                                        aria-label="{{ __('Delete folder') }}"
                                        onclick="return confirm('{{ __('Delete this folder and everything inside it?') }}')"
                                    >
                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @foreach ($files as $file)
                        @php
                            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                            $fileIcon = match ($extension) {
                                'pdf' => 'fa-solid fa-file-pdf',
                                'csv' => 'fa-solid fa-file-csv',
                                'doc', 'docx' => 'fa-solid fa-file-word',
                                'xls', 'xlsx' => 'fa-solid fa-file-excel',
                                'ppt', 'pptx' => 'fa-solid fa-file-powerpoint',
                                'zip', 'rar', '7z', 'tar', 'gz' => 'fa-solid fa-file-zipper',
                                'txt', 'log', 'md' => 'fa-solid fa-file-lines',
                                'json', 'xml', 'yaml', 'yml' => 'fa-solid fa-file-code',
                                default => 'fa-solid fa-file',
                            };
                            $fileSize = $file['size'] < 1024
                                ? $file['size'].' B'
                                : ($file['size'] < 1048576
                                    ? number_format($file['size'] / 1024, 1).' KB'
                                    : number_format($file['size'] / 1048576, 1).' MB');
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if (str_starts_with((string) $file['mime'], 'image/'))
                                        <x-avatar :src="$file['url']" />
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary-emphasis flex-shrink-0" style="width: 2.5; height: 2.5;">
                                            <i class="{{ $fileIcon }}" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                    <span>
                                        <span class="fw-medium text-break" title="{{ $file['name'] }}">{{ $file['name'] }}</span>
                                        @if ($search && dirname($file['path']) !== ($path ?: '.'))
                                            <small class="d-block text-body-secondary">{{ dirname($file['path']) }}</small>
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="small text-body-secondary">{{ $file['mime'] ?: __('File') }}</td>
                            <td class="small text-body-secondary">{{ date('M j, Y', $file['modified']) }}</td>
                            <td class="small text-body-secondary">{{ $fileSize }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ $file['url'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary" title="{{ __('Open public URL') }}" aria-label="{{ __('Open public URL') }}">
                                        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                    </a>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="{{ __('Copy URL') }}"
                                        aria-label="{{ __('Copy URL') }}"
                                        data-copy-url="{{ $file['url'] }}"
                                        data-copy-success="{{ __('URL copied to clipboard') }}"
                                        data-copy-failure="{{ __('Could not copy URL') }}"
                                    >
                                        <i class="fa-solid fa-copy" aria-hidden="true"></i>
                                    </button>
                                    <a href="{{ route('filemanager.download', ['path' => $file['path']]) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Download') }}" aria-label="{{ __('Download') }}">
                                        <i class="fa-solid fa-download" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST" action="{{ route('filemanager.delete') }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="path" value="{{ $file['path'] }}">
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="{{ __('Delete') }}"
                                            aria-label="{{ __('Delete') }}"
                                            onclick="return confirm('{{ __('Delete this file?') }}')"
                                        >
                                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($directories->isEmpty() && $files->isEmpty())
            <div class="p-3">
                <x-empty-state
                    icon="fa-solid fa-folder-open"
                    :title="$search ? __('No matching files') : __('This folder is empty')"
                    :description="$search ? __('Try a different search term.') : __('Upload a file or create a folder to get started.')"
                    compact
                />
            </div>
        @endif
    </div>
</div>

@if ($allowUpload)
<div class="modal fade" id="uploadFilesModal" tabindex="-1" aria-labelledby="uploadFilesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('filemanager.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="path" value="{{ $path }}">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="uploadFilesModalLabel">@lang('Upload files')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <label for="upload-files" class="form-label">@lang('Choose files')</label>
                    <input id="upload-files" type="file" name="files[]" multiple required class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <x-button type="submit">@lang('Upload')</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{ route('filemanager.folder') }}">
                @csrf
                <input type="hidden" name="path" value="{{ $path }}">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="createFolderModalLabel">@lang('New folder')</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <label for="folder-name" class="form-label">@lang('Folder name')</label>
                    <input id="folder-name" type="text" name="name" required maxlength="100" pattern="[^\\/]+" class="form-control" placeholder="{{ __('Folder name') }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <x-button type="submit">@lang('Create folder')</x-button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="visually-hidden" role="status" aria-live="polite" data-copy-feedback></div>

<script>
    const copyToClipboard = async (text) => {
        if (navigator.clipboard?.writeText) {
            try {
                await navigator.clipboard.writeText(text);

                return;
            } catch {
                // Fall back for browsers or contexts that deny clipboard access.
            }
        }

        const textArea = document.createElement('textarea');
        const activeElement = document.activeElement;
        textArea.value = text;
        textArea.setAttribute('readonly', '');
        textArea.style.position = 'fixed';
        textArea.style.opacity = '0';
        document.body.append(textArea);

        try {
            textArea.focus();
            textArea.select();

            if (!document.execCommand('copy')) {
                throw new Error('Clipboard copy failed.');
            }
        } finally {
            textArea.remove();

            if (activeElement instanceof HTMLElement) {
                activeElement.focus();
            }
        }
    };

    document.querySelectorAll('[data-copy-url]').forEach((button) => {
        button.addEventListener('click', async () => {
            const feedback = document.querySelector('[data-copy-feedback]');
            const icon = button.querySelector('i');

            try {
                await copyToClipboard(button.dataset.copyUrl);
                feedback.textContent = button.dataset.copySuccess;
                icon.classList.replace('fa-copy', 'fa-check');
            } catch {
                feedback.textContent = button.dataset.copyFailure;
                icon.classList.replace('fa-copy', 'fa-triangle-exclamation');
            }

            window.setTimeout(() => {
                icon.classList.remove('fa-check', 'fa-triangle-exclamation');
                icon.classList.add('fa-copy');
            }, 2000);
        });
    });
</script>
@endsection
