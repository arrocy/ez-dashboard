<?php

namespace App\Http\Controllers;

use App\Jobs\TranslateLanguage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;

class WelcomeController extends Controller
{
    public function index(): View
    {
        if (! file_exists(base_path('lang/en.json'))) {
            \Helper::createEnLanguage([
                base_path('app'),
                base_path('arrocy'),
                base_path('resources'),
                base_path('routes'),
            ]);
        }

        $browserLangs = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en';
        $array = explode(',', $browserLangs);
        $browserLang = trim($array[0]);
        $existingLangs = \Helper::existingLangs();
        if ($browserLang !== 'en' && ! in_array($browserLang, $existingLangs)) {
            $english = json_decode(File::get(base_path('lang/en.json')), true);
            $batches = array_chunk($english, 100, true);
            $jobs = [];
            foreach ($batches as $batch) {
                $jobs[] = new TranslateLanguage($browserLang, $batch);
            }
            if (count($jobs) > 0) {
                dispatch($jobs[0]->chain(array_slice($jobs, 1)));
            }
        }

        return view('welcome.index');
    }
}
