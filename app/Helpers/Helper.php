<?php

namespace App\Helpers;

use App\Jobs\TranslateLanguage;
use App\Models\Country;
use Arrocy\Saas\Models\Order;
use Arrocy\Saas\Models\Paygate;
use Arrocy\Saas\Models\Pricing;
use Arrocy\Saas\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Helper
{
    public static function isLocalhost(?string $host = null)
    {
        $host = $host ?? request()->getHost();

        return Str::contains($host, ['.test', '127.0.0.1', '192.168.1', 'localhost', '::1']);
    }

    private static function generateLogEntry($data)
    {
        if ($data instanceof Response) {
            $data = [
                'status' => $data->status(),
                'ok' => $data->ok(),
                'successful' => $data->successful(),
                'failed' => $data->failed(),
                'unauthorized' => $data->unauthorized(),
                'body' => $data->json() ?? $data->body(),
                // 'headers' => $data->headers(),
                'type' => 'laravel http client response',
            ];
        }

        if (is_object($data)) {
            if (method_exists($data, 'toArray')) {
                $data = $data->toArray();
                $data['type'] = 'laravel collection';
            } else {
                $data = json_decode(json_encode($data), true);
                $data['type'] = 'json object';
            }
        }

        if (is_array($data)) {
            if (empty($data['type'])) {
                $data['type'] = 'array';
            }
            $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $timezone = config('app.timezone') !== 'UTC' ? config('app.timezone') : env('APP_TIMEZONE', 'UTC');
        $timestamp = (new \DateTime('now', new \DateTimeZone($timezone)))->format('Y-m-d H:i:s');

        return sprintf("[%s] %s (%s)\n%s\n\n",
            $timestamp,
            request()->getHost(),
            request()->ip(),
            $data,
        );
    }

    public static function appendLog($data, ?string $filename = null): void
    {
        $filename = Str::slug($filename ?? 'append_log', '_');
        $filePath = storage_path("logs/{$filename}.log");

        $newEntry = self::generateLogEntry($data);

        file_put_contents($filePath, $newEntry, FILE_APPEND | LOCK_EX);

        if (filesize($filePath) > 100_000) {
            $file = fopen($filePath, 'r');
            if ($file) {
                fseek($file, -50_000, SEEK_END);
                fgets($file); // Consume partial cut line

                $tempStream = fopen('php://temp', 'r+');
                stream_copy_to_stream($file, $tempStream);
                fclose($file);

                rewind($tempStream);
                file_put_contents($filePath, $tempStream, LOCK_EX);
                fclose($tempStream);
            }

            return;
        }
    }

    public static function prependLog($data, ?string $filename = null): void
    {
        $filename = Str::slug($filename ?? 'prepend_log', '_');
        $filePath = storage_path("logs/{$filename}.log");

        $newEntry = self::generateLogEntry($data);

        if (! file_exists($filePath)) {
            file_put_contents($filePath, $newEntry, LOCK_EX);

            return;
        }

        $maxSize = 100_000;
        $shaveSize = 50_000;
        $currentSize = filesize($filePath);

        $tempStream = fopen('php://temp', 'r+');
        fwrite($tempStream, $newEntry);

        $logStream = fopen($filePath, 'r');
        if ($logStream) {
            if (($currentSize + strlen($newEntry)) > $maxSize) {
                $remainingSpace = $shaveSize - strlen($newEntry);

                if ($remainingSpace > 0) {
                    stream_copy_to_stream($logStream, $tempStream, $remainingSpace);
                }
                fclose($logStream);

                rewind($tempStream);
                $contents = stream_get_contents($tempStream);
                $lastNewline = strrpos($contents, "\n");
                if ($lastNewline !== false) {
                    $contents = substr($contents, 0, $lastNewline + 1);
                }

                file_put_contents($filePath, $contents, LOCK_EX);
            } else {
                stream_copy_to_stream($logStream, $tempStream);
                fclose($logStream);

                rewind($tempStream);
                file_put_contents($filePath, $tempStream, LOCK_EX);
            }
        }

        fclose($tempStream);
    }

    public static function getCountries(): array
    {
        return Cache::remember('country_all', now()->addHours(6), fn () => Country::all()->toArray());
    }

    public static function getMyCountry($arg = ''): string
    {
        $ipAddress = request()->ip() ?? '127.0.0.1';

        $addIp = self::isLocalhost($ipAddress) ? '' : "/{$ipAddress}";

        $res = Cache::remember("ip_info_{$ipAddress}", now()->addDays(7), function () use ($addIp) {
            return Http::withoutVerifying()->withOptions(['verify' => false])->get("https://ipinfo.io{$addIp}?token=0a884b273313a0")->json();
        });

        if (isset($res['country']) && $res['country'] === 'ZZ') {
            $res['country'] = 'ID';
        }

        $countryCode = $res['country'] ?? 'ID';

        if ($arg === 'code') {
            return $countryCode;
        }
        $myCountry = collect(self::getCountries())->where('id', $countryCode)->first() ?? [];
        if (empty($arg)) {
            return $myCountry;
        } else {
            return $myCountry[$arg] ?? null;
        }
    }

    public static function getCountryCallingCodes(): array
    {
        return Cache::remember('country_calling_codes', now()->addHours(6), function () {
            return collect(self::getCountries())->pluck('calling')->filter()->unique()->sortByDesc(fn ($code) => strlen($code))->values()->toArray();
        });
    }

    public static function getCountryCallingCode($number): ?string
    {
        $number = preg_replace('/\D/', '', $number);
        $callingCodes = self::getCountryCallingCodes();

        foreach ($callingCodes as $callingCode) {
            if (Str::startsWith($number, $callingCode)) {
                return $callingCode;
            }
        }

        return null;
    }

    public static function getCountry($number): string
    {
        $callingCode = self::getCountryCallingCode($number);

        return collect(self::getCountries())->where('calling', $callingCode)->value('name') ?? 'World';
    }

    public static function getTimezones()
    {
        return Cache::remember('country_timezones', now()->addHours(6), function () {
            return collect(self::getCountries())->pluck('timezone')->filter()->unique()->sort()->values()->toArray();
        });
    }

    public static function getCurrencies()
    {
        return Cache::remember('country_currencies', now()->addHours(6), function () {
            return collect(self::getCountries())->pluck('currency')->filter()->map(function ($item) {
                return is_string($item) ? json_decode($item, true) : $item;
            })->filter()->unique('code')->sortBy('name')->values()->toArray();
        });
    }

    public static function getLanguages()
    {
        // Cache::forget('country_languages');
        return Cache::remember('country_languages', now()->addHours(6), function () {
            return collect(self::getCountries())->pluck('language')->filter()->map(function ($item) {
                return is_string($item) ? json_decode($item, true) : $item;
            })->filter()->unique('code')->sortBy('name')->values()->toArray();
        });
    }

    public static function getLanguage($locale)
    {
        $locale = strtolower($locale);
        if ($locale !== 'pt-br') {
            $locale = substr($locale, 0, 2);
        }

        // Cache::forget('country_language_br');
        return Cache::remember("country_language_{$locale}", now()->addHours(6), function () use ($locale) {
            $lang = collect(self::getLanguages())->first(fn ($lang) => isset($lang['code']) && $lang['code'] === $locale);

            if ($lang) {
                return $lang;
            }

            // Fallbacks
            return match ($locale) {
                'br' => ['code' => 'br', 'flag' => 'fr', 'name' => 'Breton'],
                'nb' => ['code' => 'nb', 'flag' => 'no', 'name' => 'Norwegian Bokmål'],
                'te' => ['code' => 'te', 'flag' => 'in', 'name' => 'Telugu'],
                default => ['code' => $locale, 'flag' => 'xx', 'name' => "Unknown ({$locale})"],
            };
        });
    }

    public static function existingLangs()
    {
        // return Cache::remember('existing_langs', now()->addHours(6), fn() =>
        return array_map(fn ($file) => pathinfo($file, PATHINFO_FILENAME), glob(base_path('lang/*.json')) ?? []);
        // );
    }

    public static function jsonToArray($json)
    {
        if (is_array($json)) {
            return $json;
        }

        if ($json instanceof Collection) {
            return $json->toArray();
        }

        if (! is_string($json)) {
            return null;
        }

        $json = trim($json);
        if ($json === '') {
            return [];
        }

        $array = json_decode($json, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $array;
        }

        // Cleanup: BOM, newlines, comments, trailing commas
        $json = preg_replace('/^\xEF\xBB\xBF/', '', $json);
        $json = str_replace(["\r\n", "\r"], "\n", $json);
        $json = preg_replace('#/\*.*?\*/#s', '', $json);
        $json = preg_replace('#//.*$#m', '', $json);
        $json = preg_replace('/,\s*(?=[}\]])/', '', $json);
        $json = preg_replace('/,\s*$/', '', $json);

        $array = json_decode($json, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $array;
        }

        // convert single-quoted strings to double-quoted strings (naive but useful)
        $json = preg_replace_callback(
            "/'([^'\\\\]*(?:\\\\.[^'\\\\]*)*)'/s",
            fn ($m) => '"'.str_replace('"', '\"', $m[1]).'"',
            $json
        );

        $array = json_decode($json, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $array;
        }

        // attempt to close unclosed braces/brackets
        $openBraces = substr_count($json, '{');
        $closeBraces = substr_count($json, '}');
        $openBrackets = substr_count($json, '[');
        $closeBrackets = substr_count($json, ']');

        if ($openBraces > $closeBraces) {
            $json .= str_repeat('}', $openBraces - $closeBraces);

            $array = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $array;
            }
        }
        if ($openBrackets > $closeBrackets) {
            $json .= str_repeat(']', $openBrackets - $closeBrackets);

            $array = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $array;
            }
        }

        // fallback: try one more aggressive trim of dangling commas and attempt decode
        $json = preg_replace('/,\s*(?=(\s*[}\]]))/', '', $json);
        $json = preg_replace('/,\s*$/', '', $json);

        $array = json_decode($json, true);

        return json_last_error() === JSON_ERROR_NONE ? $array : null;
    }

    public static function afterNewUserCreated($user)
    {
        if (! class_exists(\Arrocy\Saas\SaasServiceProvider::class)) {
            return;
        }

        $plan = Pricing::where('name', 'free')->first();
        if (empty($plan)) {
            $planData = config('saas.pricings.default')[0];
            $plan = Pricing::create($planData);
        }

        $paygate = Paygate::where('name', 'offline')->first();
        if (empty($paygate)) {
            $paygateData = config('saas.paygates.default')[0];
            $paygate = Paygate::create($paygateData);
        }
        $order = Order::create([
            'reference' => 'initial_order',
            'user_id' => $user->id,
            'pricing_id' => $plan->id,
            'paygate_id' => $paygate->id,
            'amount' => $plan->price,
            'currency' => $paygate->currency_code ?? 'USD',
            'currency_amount' => $plan->price * $paygate->currency_rate,
            'status' => 'paid',
        ]);

        $starts_at = Carbon::now();
        $ends_at = $starts_at->copy()->addDays($plan->duration);
        Subscription::create([
            'id' => $user->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'pricing_id' => $plan->id,
            'paygate_id' => $paygate->id,
            'status' => 'active',
            'starts_at' => $starts_at,
            'ends_at' => $ends_at,
        ]);
    }

    public static function createEnLanguage($directories = [])
    {
        $phpFiles = [];
        $content = '';

        foreach ($directories as $dir) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $phpFiles[] = $file->getPathname();
                }
            }
        }

        foreach ($phpFiles as $path) {
            $content .= File::get($path);
        }

        // preg_match_all("/__\(\s*(['\"])([^\n\r]*?)\\1/", $content, $matches);
        preg_match_all("/(?:__|@lang)\(\s*(['\"])([^\n\r]*?)\\1\s*/", $content, $matches);

        $translations = array_unique($matches[2]);
        $translationMap = [];

        foreach ($translations as $key) {
            if (in_array($key, ['auth.password', 'some.key'])) {
                continue;
            }
            $translationMap[$key] = $key;
        }

        asort($translationMap, SORT_NATURAL | SORT_FLAG_CASE);

        File::put(
            base_path('lang/en.json'),
            json_encode($translationMap, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $syncLanguages = self::syncLanguages();

        $summary = [
            'message' => __('English translation was created successfully'),
            'Scanned directories' => count($directories).' directories',
            'Scanned PHP files' => count($phpFiles).' files',
            'Matches found' => count($matches[2]).' raw matches',
            'Unique translations' => count($translationMap).' unique translations',
            'sync' => $syncLanguages,
        ];

        self::prependLog($summary, 'new_en_json');

        return [
            'alert-type' => 'success',
            ...$summary,
        ];
    }

    public static function syncLanguages($langs = [])
    {
        $english = json_decode(File::get(base_path('lang/en.json')), true);

        if (empty($langs)) {
            $langs = self::existingLangs();
        }

        $summary = [];

        foreach ($langs as $lang) {
            if ($lang === 'en') {
                continue;
            }

            $path = base_path("lang/{$lang}.json");
            $parsed = json_decode(File::get($path), true) ?? [];

            $originalKeys = array_keys($parsed);

            // Remove unused keys
            $parsed = array_filter($parsed, fn ($value, $key) => array_key_exists($key, $english), ARRAY_FILTER_USE_BOTH);
            File::put($path, json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $removedKeys = array_diff($originalKeys, array_keys($parsed));

            // Add missing keys
            $newKeys = array_diff_key($english, $parsed);
            TranslateLanguage::dispatch($lang, $newKeys);

            $summary[$lang] = [
                'added' => count($newKeys),
                'removed' => count($removedKeys),
            ];
        }

        return $summary;
    }

    public static function fetchOpenRouterModels()
    {
        // Cache::deleteMultiple(['openrouter_models','deepseek_models','gemini_models','openai_models']);
        return Cache::remember('openrouter_models', now()->addHours(6), function () {
            try {
                $response = Http::get('https://openrouter.ai/api/v1/models')->json('data');

                $ids = array_column($response, 'id');
                $ids = array_filter($ids, function ($id) {
                    return ! (Str::contains($id, ['ai21/', 'aion-labs/', 'alfredpros/', 'alibaba/', 'allenai/', 'alpindale/', 'amazon/', 'anthracite-org/', 'arcee-ai/', 'baidu/', 'bytedance-seed/', 'cohere/', 'inception/', 'inflection/', 'liquid/', 'mistralai/', 'moonshotai/', 'morph/', 'nex-agi/', 'sao10k/', 'thedrummer/', 'xiaomi/', 'z-ai/']) || preg_match('/\d{4}-\d{2}-\d{2}$/', $id));
                });
                sort($ids);

                return array_values($ids);
            } catch (\Exception $e) {
                return ['openrouter/free', 'openai/gpt-3.5-turbo', 'openai/gpt-4o', 'anthropic/claude-3-opus', 'google/gemini-pro'];
            }
        });
    }

    public static function fetchDeepseekAiModels()
    {
        return Cache::remember('deepseek_models', now()->addHours(6), function () {
            $apikey = env('DEEPSEEK_API_KEY', '');
            if (empty($apikey)) {
                return ['deepseek-chat', 'deepseek-reasoner'];
            }
            try {
                $response = Http::withToken($apikey)
                    ->withHeaders(['Accept' => 'application/json'])
                    ->get('https://api.deepseek.com/models')
                    ->json('data');

                $ids = array_column($response, 'id');

                return array_values($ids);
            } catch (\Exception $e) {
                return ['deepseek-chat', 'deepseek-reasoner'];
            }
        });
    }

    public static function fetchGeminiAiModels()
    {
        return Cache::remember('gemini_models', now()->addHours(6), function () {
            $apikey = env('GEMINI_API_KEY', '');
            if (empty($apikey)) {
                return ['gemini-1.5-pro', 'gemini-1.5-flash', 'gemini-2.0-flash'];
            }
            try {
                $response = Http::get("https://generativelanguage.googleapis.com/v1/models?key={$apikey}")
                    ->json('models');

                $names = array_column($response, 'name');
                $names = array_map(fn ($name) => str_replace('models/', '', $name), $names);
                sort($names);

                return array_values($names);
            } catch (\Exception $e) {
                return ['gemini-1.5-pro', 'gemini-1.5-flash', 'gemini-2.0-flash'];
            }
        });
    }

    public static function fetchOpenAiModels()
    {
        return Cache::remember('openai_models', now()->addHours(6), function () {
            $apikey = env('OPENAI_API_KEY', '');
            if (empty($apikey)) {
                return ['gpt-3.5-turbo', 'gpt-4o', 'dall-e-2', 'dall-e-3'];
            }
            try {
                $response = Http::withToken($apikey)
                    ->withHeaders(['OpenAI-Beta' => 'assistants=v2'])
                    ->get('https://api.openai.com/v1/models')
                    ->json('data');

                $ids = array_column($response, 'id');
                $ids = array_filter($ids, function ($id) {
                    return ! (Str::contains($id, ['preview', '-002', '-0125', '-0914', '-1106']) || preg_match('/\d{4}-\d{2}-\d{2}$/', $id));
                });
                sort($ids);

                return array_values($ids);
            } catch (\Exception $e) {
                return ['gpt-3.5-turbo', 'gpt-4o', 'dall-e-2', 'dall-e-3'];
            }
        });
    }

    public static function csvDownload(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                throw new \RuntimeException('Unable to open the CSV output stream.');
            }

            fputcsv($output, $headers, ',', '"', '');

            foreach ($rows as $row) {
                fputcsv($output, array_map(self::spreadsheetSafeValue(...), $row), ',', '"', '');
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private static function spreadsheetSafeValue(mixed $value): string
    {
        $value = (string) ($value ?? '');

        return preg_match('/^[\s]*[=+\-@]/', $value) === 1 ? "'".$value : $value;
    }

    public static function writeEnvironment(array $values): bool
    {
        $path = base_path('.env');
        $contents = file_exists($path)
            ? file_get_contents($path)
            : (file_exists(base_path('.env.example')) ? file_get_contents(base_path('.env.example')) : false);

        if ($contents === false) {
            return false;
        }

        foreach ($values as $key => $value) {
            $line = $key.'='.self::quoteEnvironmentValue($value);
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

    private static function quoteEnvironmentValue(string $value): string
    {
        return '"'.str_replace(
            ['\\', '"', '$', "\r", "\n"],
            ['\\\\', '\\"', '\\$', '\\r', '\\n'],
            $value,
        ).'"';
    }
}
