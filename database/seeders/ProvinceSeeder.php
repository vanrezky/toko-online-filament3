<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countryId = Country::where('iso', 'ID')->first();
        $response = Http::get('https://vanrezky.github.io/api-wilayah-indonesia/api/provinces.json')->collect();

        if ($response) {
            $response->each(function ($province) use ($countryId) {
                $provinceCreate = Province::create([
                    'name' => $province['name'],
                    'country_id' => $countryId->id,
                    'rajaongkir' => 0,
                ]);

                $responseCities = Http::get("https://vanrezky.github.io/api-wilayah-indonesia/api/regencies/{$province['id']}.json")->collect();

                $responseCities->each(function ($city) use ($provinceCreate) {
                    $cityCreate = $provinceCreate->cities()->create([
                        'name' => $city['name'],
                        'rajaongkir' => 0,
                        'type' => 'regency',
                    ]);

                    $responseSubDistricts = Http::get("https://vanrezky.github.io/api-wilayah-indonesia/api/districts/{$city['id']}.json")->collect();

                    $responseSubDistricts->each(function ($subDistrict) use ($cityCreate) {
                        $cityCreate->subDistricts()->create([
                            'name' => $subDistrict['name'],
                            'rajaongkir' => 0,
                            'postal_code' => rand(10000, 99999),
                        ]);
                    });
                });
            });
        }
    }
}
