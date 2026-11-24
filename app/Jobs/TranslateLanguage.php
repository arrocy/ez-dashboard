<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateLanguage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3290;
    protected $lang;
    protected $batch;

    public function __construct($lang, $batch)
    {
        $this->lang = $lang;
        $this->batch = $batch;
    }

    public function handle(): void
    {
        $targetLang = $this->lang;
        $filePath = base_path("lang/{$targetLang}.json");
        $parsed = File::exists($filePath) ? json_decode(File::get($filePath), true) : [];

        // 1. Comprehensive list of words that often translate poorly in tech contexts
        $do_not_translate_words = [
            'API', 'Api', 'api', 
            'Paygate', 'Paygates', 'paygate', 'paygates', 
            'Admin', 'admin', 'Administrator', 'administrator', 
            'Database', 'database',
            'URL', 'url', 'URLs', 'urls',
            'SMS', 'sms',
            'OTP', 'otp',
            'ID', 'ids', 'IDs',
            'JSON', 'json', 'XML', 'xml',
            'Webhooks', 'webhook', 'Webhooks', 'webhooks',
            'Token', 'token', 'Tokens', 'tokens',
            'Meta', 'meta',
            'Cron', 'cron',
        ];

        foreach ($this->batch as $key => $val) {
            $placeholders = [];
            $sourceText = $val;
            $tokenCounter = 0;

            // 2. Protect specific system words first (using word boundaries to prevent partial matches)
            foreach ($do_not_translate_words as $word) {
                // Only match whole words (e.g., 'admin' won't accidentally match inside 'administrator')
                $pattern = '/\b' . preg_quote($word, '/') . '\b/';
                
                if (preg_match($pattern, $sourceText)) {
                    $token = "<span class=\"notranslate\">__WORD_{$tokenCounter}__</span>";
                    $placeholders[$token] = $word;
                    $sourceText = preg_replace($pattern, $token, $sourceText);
                    $tokenCounter++;
                }
            }

            // 3. Protect Laravel placeholders ({bracket} or :colon formats)
            preg_match_all('/\{[^\}]+\}|:[a-zA-Z_0-9]+/', $sourceText, $matches);
            foreach ($matches[0] as $placeholder) {
                $token = "<span class=\"notranslate\">__PARAM_{$tokenCounter}__</span>";
                $placeholders[$token] = $placeholder;
                $sourceText = str_replace($placeholder, $token, $sourceText);
                $tokenCounter++;
            }

            // 4. Translate with protected blocks completely isolated
            $translated = GoogleTranslate::trans($sourceText, $targetLang, 'en', ['verify' => false]);

            // 5. Restore everything perfectly in reverse order
            foreach ($placeholders as $token => $originalValue) {
                $translated = str_replace($token, $originalValue, $translated);
                $translated = str_replace(str_replace('"', "'", $token), $originalValue, $translated); 
            }

            $parsed[$key] = trim($translated);
        }

        ksort($parsed, SORT_NATURAL | SORT_FLAG_CASE);
        File::put($filePath, json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    // public function handle(): void
    // {
    //     $targetLang = $this->lang;

    //     // Load existing translations if file exists
    //     $filePath = base_path("lang/{$targetLang}.json");
    //     $parsed = File::exists($filePath) ? json_decode(File::get($filePath), true) : [];

    //     foreach ($this->batch as $key => $val) {
    //         $placeholders = [];
    //         $sourceText = $val;

    //         // Matches BOTH {anything} OR :letters_and_underscores
    //         // Pattern 1: \{[^\}]+\}  -> matches {locale}
    //         // Pattern 2: :[a-zA-Z_0-9]+ -> matches :locale
    //         preg_match_all('/\{[^\}]+\}|:[a-zA-Z_0-9]+/', $sourceText, $matches);
            
    //         foreach ($matches[0] as $index => $placeholder) {
    //             // Generate a unique token wrapped in an HTML tag Google Translate ignores
    //             $token = "<span class=\"notranslate\">__TOKEN_{$index}__</span>";
    //             $placeholders[$token] = $placeholder;
    //             $sourceText = str_replace($placeholder, $token, $sourceText);
    //         }

    //         // Translate the string with placeholders safely hidden inside HTML
    //         $translated = GoogleTranslate::trans($sourceText, $targetLang, 'en', ['verify' => false]);

    //         // Restore the original placeholders
    //         foreach ($placeholders as $token => $placeholder) {
    //             // Google Translate sometimes normalizes quotes or spaces inside tags, 
    //             // so we clean up potential whitespace variations just in case.
    //             $translated = str_replace($token, $placeholder, $translated);
    //             $translated = str_replace(str_replace('"', "'", $token), $placeholder, $translated); 
    //         }

    //         // Fallback cleanup: If Google Translate accidentally added spaces around your tag, strip them
    //         $parsed[$key] = trim($translated);
    //     }

    //     // Sort naturally
    //     ksort($parsed, SORT_NATURAL | SORT_FLAG_CASE);
    //     // Save merged translations
    //     File::put($filePath, json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    // }
}