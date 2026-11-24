<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    /**
     * Get or set configuration settings.
     *
     * @return mixed
     *               setting(['company_email' => 'new_email@test.com']);
     *               setting(['supported_languages' => ['en', 'pl', 'id']]);
     *               $languages = setting('supported_languages');
     */
    function setting(string|array|null $key = null, mixed $default = null): mixed
    {
        $settings = Cache::rememberForever('settings', static fn() => Setting::pluck('value', 'key')->all());

        if (is_null($key)) return $settings;

        if (is_array($key)) {
            $hasChanges = false;

            foreach ($key as $k => $v) {
                $k = (string) $k;
                $v = (is_array($v) || is_object($v)) ? json_encode($v) : ($v ?? '');

                // Check if value actually changed before saving (prevents useless DB writes)
                if (($settings[$k] ?? null) !== $v) {
                    Setting::updateOrCreate(['key' => $k], ['value' => $v]);
                    $hasChanges = true;
                }
            }

            if ($hasChanges) Cache::forget('settings');

            return true;
        }

        $value = $settings[(string) $key] ?? null;

        if (is_null($value)) return $default;

        if (str_starts_with($value, '{') || str_starts_with($value, '[')) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) return $decoded;
        }

        return str_ends_with($key, '_enabled') ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : $value;
    }
}

// if (! function_exists('central_domain')) {
//     function central_domain(): string
//     {
//         return strtolower(parse_url((string) config('app.url'), PHP_URL_HOST) ?? '');
//     }
// }

// if (! function_exists('is_central')) {
//     function is_central($domains = []): bool
//     {
//         $central_domain = central_domain();

//         if (! empty($domains)) {
//             $domains = is_string($domains) ? [$domains] : $domains;

//             return in_array($central_domain, $domains, true);
//         }

//         return $central_domain === (request()->getHost() ?: $central_domain);
//     }
// }

if (! function_exists('write_env')) {
    function write_env(array $values): bool
    {
        $path = base_path('.env');
        $contents = file_exists($path)
            ? file_get_contents($path)
            : (file_exists(base_path('.env.example')) ? file_get_contents(base_path('.env.example')) : false);

        if ($contents === false) {
            return false;
        }

        foreach ($values as $key => $value) {
            $line = $key.'='.quoted_env_value($value);
            $pattern = '/^(?:#\s*)?'.preg_quote($key, '/').'=[^\r\n]*$/m';
            $updated = preg_replace_callback($pattern, fn (array $matches): string => $line, $contents, 1, $count);

            if ($updated === null) {
                return false;
            }

            if ($count === 0) {
                $updated = rtrim($contents, "\r\n").PHP_EOL.$line.PHP_EOL;
            }

            $contents = $updated;
        }

        return file_put_contents($path, $contents, LOCK_EX) !== false;
    }
}

if (! function_exists('quoted_env_value')) {
    function quoted_env_value(string $value): string
    {
        return '"'.str_replace(
            ['\\', '"', '$', "\r", "\n"],
            ['\\\\', '\\"', '\\$', '\\r', '\\n'],
            $value,
        ).'"';
    }
}

if (! function_exists('short_num')) {
    function short_num(float|int|null $number): string
    {
        return match (true) {
            $number >= 1e9 => number_format($number / 1e9, 1).'B',
            $number >= 1e6 => number_format($number / 1e6, 1).'M',
            $number >= 1e3 => number_format($number / 1e3, 1).'K',
            default => (string) $number,
        };
    }
}
