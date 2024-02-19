<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // curl
        $response = Http::get('https://countriesnow.space/api/v0.1/countries/flag/unicode')->collect('data');

        if ($response) {
            $response->each(function ($country) {
                Country::create([
                    'name' => $country['name'],
                    'iso' => $country['iso2'],
                    // 'iso3' => $country['iso3']
                ]);
            });
        }
    }
}
