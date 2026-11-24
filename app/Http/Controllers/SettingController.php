<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $settings = Setting::query()
            ->search($request->string('search')->toString())
            ->orderBy('key')
            ->paginate(10)
            ->withQueryString();

        $defaultKeys = array_keys(config('settings.default'));

        return view('setting.index', compact('settings', 'defaultKeys'));
    }

    public function edit(Setting $setting): View
    {
        $defaultKeys = array_keys(config('settings.default'));

        return view('setting.edit', compact('setting', 'defaultKeys'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255'],
        ]);

        setting([$validated['key'] => '']);

        return redirect()->route('setting.index')->with('success', __('Setting created successfully.'));
    }

    public function update(Request $request, Setting $setting): RedirectResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255', Rule::unique('settings', 'key')->ignore($setting)],
            'value' => ['nullable', 'string'],
        ]);

        $setting->update($validated);
        Cache::forget('settings');

        return redirect()->route('setting.index')->with('success', __('Setting updated successfully.'));
    }

    public function destroy(Request $request, Setting $setting): RedirectResponse
    {
        $defaultKeys = array_keys(config('settings.default'));

        if (in_array($setting->key, $defaultKeys)) {
            return back()->with('error', __('Default setting cannot be deleted.'));
        }

        $setting->delete();
        Cache::forget('settings');

        return redirect()->route('setting.index')->with('success', __('Setting deleted successfully.'));
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', Rule::exists('settings', 'id')],
        ]);

        $defaultKeys = array_keys(config('settings.default'));

        DB::transaction(function () use ($validated, $defaultKeys) {
            foreach ($validated['ids'] as $id) {
                $setting = Setting::find($id);
                if ($setting === null || in_array($setting->key, $defaultKeys, true)) {
                    continue;
                }

                $setting->delete();
            }
        });

        Cache::forget('settings');

        return redirect()->route('setting.index')->with('success', __('Selected settings were deleted.'));
    }

    public function export(): StreamedResponse
    {
        return \Helper::csvDownload(
            'settings.csv',
            ['ID', 'Key', 'Value'],
            Setting::query()->orderBy('id')->cursor()->map(fn (Setting $setting) => [
                $setting->getKey(),
                $setting->key,
                $setting->value,
            ]),
        );
    }
}
