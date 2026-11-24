<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $countries = json_decode(file_get_contents(base_path('countries.json')), true);
        foreach ($countries as $country) {
            Country::create([
                'id' => $country['id'],
                'name' => $country['name'],
                'calling' => $country['calling'],
                'timezone' => $country['timezone'],
                'currency' => $country['currency'],
                'language' => $country['language'],
            ]);
        }
    }
}
