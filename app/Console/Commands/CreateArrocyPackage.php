<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

// Usage: php artisan arrocy:package {name}
#[Signature('arrocy:package {name : The name of the package (e.g., support)}')]
#[Description('Create a uniform CRUD boilerplate for an Arrocy package')]
class CreateArrocyPackage extends Command
{
    public function handle(): int
    {
        $cleanName = Str::of((string) $this->argument('name'))
            ->slug()
            ->replaceMatches('/^(?:ez-)+/', '')
            ->trim('-')
            ->toString();

        if ($cleanName === '') {
            $this->error('Provide a package name containing letters or numbers.');

            return Command::FAILURE;
        }

        $packageName = "ez-{$cleanName}";
        $className = Str::studly($cleanName);
        $tableName = 'ez_'.Str::snake(Str::pluralStudly($className));
        $recordName = Str::camel($cleanName);
        $recordsName = Str::plural($recordName);

        if ($recordsName === $recordName) {
            $recordsName .= 'Records';
        }

        $packagePath = base_path("arrocy/{$packageName}");

        if (File::exists($packagePath)) {
            $this->error("Package 'arrocy/{$packageName}' already exists!");

            return Command::FAILURE;
        }

        $directories = [
            'src\\Http\\Controllers',
            'src\\Models',
            'database\\migrations',
            'database\\seeders',
            'resources\\views',
            'routes',
            'config',
            'lang',
        ];

        foreach ($directories as $directory) {
            File::makeDirectory("{$packagePath}\\{$directory}", 0755, true, true);
        }

        $files = [
            "{$packagePath}\\composer.json" => $this->getComposerStub($packageName, $className),
            "{$packagePath}\\config\\{$cleanName}.php" => $this->getConfigStub(),
            "{$packagePath}\\src\\{$className}ServiceProvider.php" => $this->getServiceProviderStub($cleanName, $className),
            "{$packagePath}\\src\\Models\\{$className}.php" => $this->getModelStub($tableName, $className),
            "{$packagePath}\\src\\Http\\Controllers\\{$className}Controller.php" => $this->getControllerStub(
                $cleanName,
                $className,
                $recordName,
                $recordsName,
            ),
            "{$packagePath}\\database\\migrations\\0001_01_03_000010_create_{$tableName}_table.php" => $this->getMigrationStub($tableName),
            "{$packagePath}\\database\\seeders\\DatabaseSeeder.php" => $this->getDatabaseSeederStub($className),
            "{$packagePath}\\routes\\web.php" => $this->getWebRoutesStub($cleanName, $className, $recordName),
            "{$packagePath}\\routes\\api.php" => $this->getApiRoutesStub($cleanName),
            "{$packagePath}\\resources\\views\\index.blade.php" => $this->getIndexStub(
                $cleanName,
                $className,
                $recordName,
                $recordsName,
            ),
            "{$packagePath}\\resources\\views\\edit.blade.php" => $this->getEditStub(
                $cleanName,
                $className,
                $recordName,
            ),
        ];

        foreach ($files as $path => $content) {
            File::put($path, $content);
        }

        $this->info("Package boilerplate structured successfully: arrocy/{$packageName}");
        $this->warn("\nNext steps:");
        $this->line('1. Add this to the main composer.json repositories block:');
        $this->line('   { "type": "path", "url": "arrocy/*" }');
        $this->line("2. Run: composer require arrocy/{$packageName}:dev-main");
        $this->line('3. Run: php artisan migrate');
        $this->line("4. Run: php artisan db:seed --class=Arrocy\\{$className}\\Database\\Seeders\\DatabaseSeeder");

        return Command::SUCCESS;
    }

    protected function getComposerStub(string $name, string $class): string
    {
        return json_encode([
            'name' => "arrocy/{$name}",
            'description' => "{$class} package for arrocy/ez-dashboard ecosystem.",
            'autoload' => [
                'psr-4' => [
                    "Arrocy\\{$class}\\" => 'src/',
                    "Arrocy\\{$class}\\Database\\Seeders\\" => 'database/seeders/',
                ],
            ],
            'extra' => [
                'laravel' => [
                    'providers' => [
                        "Arrocy\\{$class}\\{$class}ServiceProvider",
                    ],
                ],
            ],
            'require' => [
                'php' => '^8.3',
                'illuminate/database' => '^13.0',
                'illuminate/support' => '^13.0',
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
    }

    protected function getConfigStub(): string
    {
        return <<<'PHP'
<?php

return [
    'version' => '0.1.0',
];
PHP.PHP_EOL;
    }

    protected function getServiceProviderStub(string $name, string $class): string
    {
        if (Str::endsWith($name, 's')) {
            $label = Str::headline($name);
        } else {
            $label = Str::headline(Str::plural($name));
        }

        return <<<PHP
<?php

namespace Arrocy\\{$class};

use Illuminate\\Support\\ServiceProvider;

class {$class}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \$this->mergeConfigFrom(__DIR__.'/../config/{$name}.php', '{$name}');
    }

    public function boot(): void
    {
        \$this->loadJsonTranslationsFrom(__DIR__.'/../lang');
        \$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        \$this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        \$this->loadViewsFrom(__DIR__.'/../resources/views', '{$name}');
        \$this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        \$this->injectSidebarMenu();
    }

    protected function injectSidebarMenu(): void
    {
        \$items = config('menu.sidebar.workspace.items');

        if (isset(\$items)) {
            \$items[] = ['label' => '{$label}', 'icon' => 'fa-solid fa-cubes', 'route' => '{$name}.index'];
            config(['menu.sidebar.workspace.items' => \$items]);
        }
    }
}
PHP;
    }

    protected function getModelStub(string $table, string $class): string
    {
        return <<<PHP
<?php

namespace Arrocy\\{$class}\\Models;

use Illuminate\\Database\\Eloquent\\Attributes\\Fillable;
use Illuminate\\Database\\Eloquent\\Builder;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;

#[Fillable(['user_id', 'enabled', 'name', 'avatar', 'data'])]
class {$class} extends Model
{
    protected \$table = '{$table}';

    protected \$casts = [
        'data' => 'array',
    ];

    public function scopeSearch(Builder \$query, ?string \$search): Builder
    {
        \$search = trim((string) \$search);

        if (\$search === '') {
            return \$query;
        }

        \$pattern = '%'.\$search.'%';

        return \$query->where(fn (Builder \$query): Builder => \$query
            ->where('name', 'like', \$pattern)
            ->orWhere('data', 'like', \$pattern));
    }

    public function user(): BelongsTo
    {
        return \$this->belongsTo(config('auth.providers.users.model'));
    }
}
PHP;
    }

    protected function getMigrationStub(string $table): string
    {
        return <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table): void {
            \$table->id();
            \$table->foreignId('user_id')->constrained()->cascadeOnDelete();
            \$table->boolean('enabled')->default(true);
            \$table->string('name');
            \$table->string('avatar')->nullable();
            \$table->json('data')->nullable();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;
    }

    protected function getDatabaseSeederStub(string $class): string
    {
        return <<<PHP
<?php

namespace Arrocy\\{$class}\\Database\\Seeders;

use Arrocy\\{$class}\\Models\\{$class};
use Illuminate\\Database\\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        {$class}::updateOrCreate(['user_id' => config('app.admin_id')], [
            'enabled' => true,
            'name' => 'Sample {$class}',
            'avatar' => null,
            'data' => ['key' => 'value'],
        ]);
    }
}
PHP;
    }

    protected function getWebRoutesStub(string $name, string $class, string $record): string
    {
        return <<<PHP
<?php

use Arrocy\\{$class}\\Http\\Controllers\\{$class}Controller;
use Illuminate\\Support\\Facades\\Route;

Route::middleware(['web', 'auth', 'verified'])->prefix('{$name}')->name('{$name}.')->group(function (): void {
    Route::get('/', [{$class}Controller::class, 'index'])->name('index');
    Route::post('/', [{$class}Controller::class, 'store'])->name('store');
    Route::post('/import', [{$class}Controller::class, 'import'])->name('import');
    Route::get('/export', [{$class}Controller::class, 'export'])->name('export');
    Route::get('/{{$record}}/edit', [{$class}Controller::class, 'edit'])->name('edit');
    Route::put('/{{$record}}', [{$class}Controller::class, 'update'])->name('update');
    Route::get('/{{$record}}', [{$class}Controller::class, 'toggle'])->name('toggle');
    Route::delete('/{{$record}}', [{$class}Controller::class, 'destroy'])->name('destroy');
    Route::delete('/', [{$class}Controller::class, 'bulkDelete'])->name('bulk-destroy');
});
PHP;
    }

    protected function getApiRoutesStub(string $name): string
    {
        return <<<PHP
<?php

use Illuminate\\Support\\Facades\\Route;

Route::middleware(['api'])->prefix('api/{$name}')->name('{$name}.api.')->group(function (): void {
    // Add package API routes here.
});
PHP;
    }

    protected function getControllerStub(
        string $name,
        string $class,
        string $record,
        string $records,
    ): string {
        $label = Str::headline($class);
        $singularLabel = Str::lower($label);
        $pluralLabel = Str::plural($label);

        return <<<PHP
<?php

namespace Arrocy\\{$class}\\Http\\Controllers;

use App\\Http\\Controllers\\Controller;
use Arrocy\\{$class}\\Models\\{$class};
use Illuminate\\Http\\RedirectResponse;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Validator;
use Illuminate\\Validation\\ValidationException;
use Illuminate\\View\\View;
use Symfony\\Component\\HttpFoundation\\StreamedResponse;

class {$class}Controller extends Controller
{
    public function index(Request \$request): View
    {
        \$request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        \${$records} = {$class}::query()
            ->where('user_id', auth()->id())
            ->search(\$request->string('search')->toString())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('{$name}::index', compact('{$records}'));
    }

    public function store(Request \$request): RedirectResponse
    {
        \$validated = \$request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        \${$record} = new {$class}(\$validated);
        \${$record}->user()->associate(auth()->user());
        \${$record}->save();

        return redirect()->route('{$name}.index')->with('success', __('{$label} created successfully.'));
    }

    public function edit({$class} \${$record}): View
    {
        \$this->authorizeOwner(\${$record});

        return view('{$name}::edit', compact('{$record}'));
    }

    public function update(Request \$request, {$class} \${$record}): RedirectResponse
    {
        \$this->authorizeOwner(\${$record});

        \$validated = \$request->validate([
            'enabled' => ['required', 'boolean'],
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'url:http,https'],
            'data' => ['nullable', 'json'],
        ]);

        if (isset(\$validated['data'])) {
            \$validated['data'] = \$this->decodeJsonData(\$validated['data']);
        }

        \${$record}->update(\$validated);

        return redirect()->route('{$name}.index')->with('success', __('{$label} updated successfully.'));
    }

    public function toggle({$class} \${$record}): RedirectResponse
    {
        \$this->authorizeOwner(\${$record});
        \${$record}->update(['enabled' => ! \${$record}->enabled]);

        return back()->with('success', __('{$label} updated successfully.'));
    }

    public function destroy({$class} \${$record}): RedirectResponse
    {
        \$this->authorizeOwner(\${$record});
        \${$record}->delete();

        return redirect()->route('{$name}.index')->with('success', __('{$label} deleted successfully.'));
    }

    public function bulkDelete(Request \$request): RedirectResponse
    {
        \$validated = \$request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ]);

        DB::transaction(function () use (\$validated): void {
            {$class}::query()
                ->where('user_id', auth()->id())
                ->whereIn('id', \$validated['ids'])
                ->delete();
        });

        return redirect()->route('{$name}.index')->with('success', __('Selected {$pluralLabel} were deleted.'));
    }

    public function export(): StreamedResponse
    {
        return \\Helper::csvDownload(
            '{$records}.csv',
            ['id', 'user_id', 'enabled', 'name', 'avatar', 'data', 'created_at', 'updated_at'],
            {$class}::query()
                ->where('user_id', auth()->id())
                ->orderBy('id')
                ->cursor()
                ->map(static fn ({$class} \${$record}): array => [
                    \${$record}->getKey(),
                    \${$record}->user_id,
                    \${$record}->enabled ? 1 : 0,
                    \${$record}->name,
                    \${$record}->avatar,
                    \${$record}->data === null
                        ? null
                        : json_encode(\${$record}->data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    \${$record}->created_at?->toDateTimeString(),
                    \${$record}->updated_at?->toDateTimeString(),
                ]),
        );
    }

    public function import(Request \$request): RedirectResponse
    {
        \$validated = \$request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'extensions:csv', 'max:10240'],
        ]);

        \$filePath = \$validated['file']->getRealPath();
        \$stream = \$filePath === false ? false : fopen(\$filePath, 'r');

        if (\$stream === false) {
            throw new \\RuntimeException('Unable to open the uploaded CSV file.');
        }

        \$expectedHeaders = ['enabled', 'name', 'avatar', 'data'];
        \$records = [];
        \$rowNumber = 1;

        try {
            \$headers = fgetcsv(\$stream, null, ',', '"', '');

            if (\$headers === false) {
                throw ValidationException::withMessages([
                    'file' => __('The CSV file is empty.'),
                ]);
            }

            \$headers[0] = preg_replace('/^\\xEF\\xBB\\xBF/', '', (string) \$headers[0]) ?? (string) \$headers[0];

            if (\$headers !== \$expectedHeaders) {
                throw ValidationException::withMessages([
                    'file' => __('The CSV headers must be: :headers.', [
                        'headers' => implode(',', \$expectedHeaders),
                    ]),
                ]);
            }

            while (true) {
                \$row = fgetcsv(\$stream, null, ',', '"', '');

                if (\$row === false) {
                    if (! feof(\$stream)) {
                        throw new \\RuntimeException('Unable to read the uploaded CSV file.');
                    }

                    break;
                }

                \$rowNumber++;

                if (\$row === [null]) {
                    continue;
                }

                if (count(\$row) !== count(\$expectedHeaders)) {
                    throw ValidationException::withMessages([
                        'file' => __('CSV row :row has an unexpected number of columns.', [
                            'row' => \$rowNumber,
                        ]),
                    ]);
                }

                \$attributes = array_combine(\$expectedHeaders, \$row);
                \$attributes['avatar'] = \$attributes['avatar'] === '' ? null : \$attributes['avatar'];
                \$attributes['data'] = \$attributes['data'] === '' ? null : \$attributes['data'];

                \$validator = Validator::make(\$attributes, [
                    'enabled' => ['sometimes', 'boolean'],
                    'name' => ['required', 'string', 'max:255'],
                    'avatar' => ['nullable', 'url:http,https'],
                    'data' => ['nullable', 'json'],
                ]);

                if (\$validator->fails()) {
                    throw ValidationException::withMessages([
                        'file' => __('CSV row :row is invalid: :error', [
                            'row' => \$rowNumber,
                            'error' => \$validator->errors()->first(),
                        ]),
                    ]);
                }

                \$attributes = \$validator->validated();

                if (isset(\$attributes['data'])) {
                    \$attributes['data'] = \$this->decodeJsonData(\$attributes['data'], \$rowNumber);
                }

                \$records[] = \$attributes;
            }
        } finally {
            fclose(\$stream);
        }

        if (\$records === []) {
            throw ValidationException::withMessages([
                'file' => __('The CSV file does not contain any records.'),
            ]);
        }

        DB::transaction(function () use (\$records): void {
            foreach (\$records as \$attributes) {
                \${$record} = new {$class}(\$attributes);
                \${$record}->user()->associate(auth()->user());
                \${$record}->save();
            }
        });

        return redirect()->route('{$name}.index')->with('success', __('Imported :count {$pluralLabel}.', [
            'count' => count(\$records),
        ]));
    }

    private function decodeJsonData(string \$data, ?int \$rowNumber = null): array
    {
        \$decodedData = json_decode(\$data, true, flags: JSON_THROW_ON_ERROR);

        if (! is_array(\$decodedData)) {
            \$field = \$rowNumber === null ? 'data' : 'file';
            \$message = \$rowNumber === null
                ? __('The data field must contain a JSON object or array.')
                : __('CSV row :row must contain data as a JSON object or array.', ['row' => \$rowNumber]);

            throw ValidationException::withMessages([\$field => \$message]);
        }

        return \$decodedData;
    }

    private function authorizeOwner({$class} \${$record}): void
    {
        abort_unless(\${$record}->user_id === auth()->id(), 403, __('You are not the owner of this {$singularLabel}.'));
    }
}
PHP;
    }

    protected function getIndexStub(string $name, string $class, string $record, string $records): string
    {
        $label = Str::headline($name);
        $recordLabel = Str::headline($class);
        $recordsLabel = Str::plural($recordLabel);

        return <<<BLADE
@extends('layouts.main')

@section('title', __('{$recordsLabel}'))
@section('page-heading', __('{$recordsLabel} overview'))

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">@lang('{$recordsLabel}')</h1>
            <p class="text-body-secondary mb-0">@lang('Search and manage {$recordsLabel} across your workspace.')</p>
        </div>
        <x-button variant="dark" type="button" data-bs-toggle="modal" data-bs-target="#createRecordModal">
            <i class="fa-solid fa-plus me-2"></i>@lang('Add New {$recordLabel}')
        </x-button>
    </div>

    <x-card>
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
            <form action="{{ route('{$name}.index') }}" method="GET" class="d-flex gap-2">
                <div class="flex-grow-1">
                    <x-input name="search" id="search" type="search" label="" :value="request('search')" icon="fa-solid fa-magnifying-glass text-body-secondary" placeholder="Search {$recordsLabel}..." aria-label="Search {$recordsLabel}" />
                </div>
                <x-button variant="secondary" outline type="submit">@lang('Search')</x-button>
            </form>
            <div class="d-flex gap-2">
                <x-button variant="secondary" outline type="button" data-bs-toggle="modal" data-bs-target="#importRecordsModal">
                    <i class="fa-solid fa-file-import me-2"></i>@lang('Import')
                </x-button>
                <a href="{{ route('{$name}.export') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-file-export me-2"></i>@lang('Export')</a>
            </div>
        </div>

        <form action="{{ route('{$name}.bulk-destroy') }}" method="POST" data-bulk-delete-form>
            @csrf
            @method('DELETE')
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small text-body-secondary">{{ \${$records}->total() }} @lang('{$recordsLabel} found')</span>
                <x-button variant="danger" outline size="sm" type="submit" data-bulk-delete-button hidden><i class="fa-solid fa-trash-can me-1"></i>@lang('Bulk Delete')</x-button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col" style="width:2.5rem"><input class="form-check-input" type="checkbox" aria-label="Select all rows" data-select-all="input[name='ids[]']"></th>
                            <th scope="col">@lang('Name')</th>
                            <th scope="col">@lang('Data')</th>
                            <th scope="col" class="text-end">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (\${$records} as \${$record})
                            <tr>
                                <td><input class="form-check-input" type="checkbox" name="ids[]" value="{{ \${$record}->getKey() }}" aria-label="Select row"></td>
                                <td class="text-body-secondary"><div class="d-flex align-items-center gap-2"><x-avatar :src="\${$record}->avatar" :name="\${$record}->name" />{{ \${$record}->name }}</div></td>
                                <td class="text-body-secondary">{{ \${$record}->data === null ? '' : \Illuminate\Support\Str::limit(json_encode(\${$record}->data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 37, '...') }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('{$name}.toggle', \${$record}) }}" class="btn btn-sm btn-outline-{{ \${$record}->enabled ? 'success' : 'secondary' }}" title="{$label} {{ \${$record}->enabled ? 'enabled' : 'disabled' }}. Click to change!" aria-label="Toggle enabled"><i class="fa-solid fa-toggle-{{ \${$record}->enabled ? 'on' : 'off' }}"></i></a>
                                    <a href="{{ route('{$name}.edit', \${$record}) }}" class="btn btn-sm btn-outline-primary" title="Edit {$record}" aria-label="Edit {$record}"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <x-button variant="danger" outline size="sm" type="button" data-bs-toggle="modal" data-bs-target="#deleteRecordModal" :data-delete-url="route('{$name}.destroy', \${$record})" :data-delete-name="\${$record}->name" title="Delete {$record}" aria-label="Delete {$record}"><i class="fa-solid fa-trash-can"></i></x-button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-empty-state title="No {$recordsLabel} found" description="Try another search or add a new {$recordLabel}." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 border-top pt-3 mt-2">
                <span class="small text-body-secondary">@lang('Showing') {{ \${$records}->firstItem() ?? 0 }}–{{ \${$records}->lastItem() ?? 0 }} @lang('of') {{ \${$records}->total() }}</span>
                {{ \${$records}->links('pagination::bootstrap-5') }}
            </div>
        </form>
    </x-card>

    <x-modal id="deleteRecordModal" title="Delete {$recordLabel}?" data-delete-modal>
        <form id="delete-{$name}-form" method="POST" action="{{ route('{$name}.index') }}" data-delete-form>
            @csrf
            @method('DELETE')
            @lang('Are you sure you want to delete') <strong data-delete-name></strong>? @lang('This action cannot be undone.')
        </form>
        <x-slot:footer>
            <x-button variant="light" type="button" data-bs-dismiss="modal">@lang('Cancel')</x-button>
            <x-button variant="danger" type="submit" form="delete-{$name}-form">@lang('Delete {$recordLabel}')</x-button>
        </x-slot:footer>
    </x-modal>

    <x-modal id="importRecordsModal" title="Import {$recordsLabel}">
        <form id="import-{$name}-form" method="POST" action="{{ route('{$name}.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="import-file">@lang('CSV file')</label>
                <input class="form-control @error('file') is-invalid @enderror" type="file" id="import-file" name="file" accept=".csv,text/csv" required>
                @error('file')<div class="invalid-feedback d-block">{{ \$message }}</div>@enderror
            </div>
            <p class="small text-body-secondary mb-2">@lang('CSV columns and example row:')</p>
            <pre class="bg-body-tertiary p-3 rounded small"><code>enabled,name,avatar,data
1,Example,https://picsum.photos/200/300,{"key":"value"}</code></pre>
        </form>
        <x-slot:footer>
            <x-button variant="light" type="button" data-bs-dismiss="modal">@lang('Cancel')</x-button>
            <x-button variant="dark" type="submit" form="import-{$name}-form">@lang('Import')</x-button>
        </x-slot:footer>
    </x-modal>

    <x-modal id="createRecordModal" title="Add New {$recordLabel}">
        <form id="create-{$name}-form" method="POST" action="{{ route('{$name}.store') }}">
            @csrf
            <x-input name="name" id="new-name" label="Name" icon="fa-solid fa-tag" required />
        </form>
        <x-slot:footer>
            <x-button variant="light" type="button" data-bs-dismiss="modal">@lang('Cancel')</x-button>
            <x-button variant="dark" type="submit" form="create-{$name}-form">@lang('Create {$recordLabel}')</x-button>
        </x-slot:footer>
    </x-modal>
@endsection
BLADE;
    }

    protected function getEditStub(string $name, string $class, string $record): string
    {
        $label = Str::headline($class);

        return <<<BLADE
@extends('layouts.main')

@section('title', __('Edit {$label}'))
@section('page-heading', __('Edit {$label}'))

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">@lang('Edit {$label}')</h1>
        <p class="text-body-secondary mb-0">{{ \${$record}->name }}</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('{$name}.update', \${$record}) }}">
            @csrf
            @method('PUT')
            <div class="row row-cols-md-2 g-3 pb-4">
                <x-select name="enabled" id="edit-enabled" label="Enabled" :value="\${$record}->enabled">
                    <option value="1" @selected((string) old('enabled', (int) \${$record}->enabled) === '1')>Yes</option>
                    <option value="0" @selected((string) old('enabled', (int) \${$record}->enabled) === '0')>No</option>
                </x-select>
                <x-input name="name" id="edit-name" :value="\${$record}->name" label="Name" icon="fa-solid fa-tag" required />
                <x-input name="avatar" id="edit-avatar" :value="\${$record}->avatar" label="Avatar" icon="fa-solid fa-image" />
            </div>
            <div class="pb-4">
                <x-textarea name="data" id="edit-data" value="{!! old('data', \${$record}->data === null ? '' : json_encode(\${$record}->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !!}" icon="fa-solid fa-file-lines" rows="5" />
            </div>
            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="{{ route('{$name}.index') }}" class="btn btn-light">@lang('Cancel')</a>
                <x-button variant="dark" type="submit">@lang('Save changes')</x-button>
            </div>
        </form>
    </x-card>
@endsection
BLADE;
    }
}
