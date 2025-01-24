<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $country = Country::where('iso', 'ID')->first();

        // get provinces

        $fileContent = Storage::get('locations/indonesia/provinces.json');
        $provinces = json_decode($fileContent, true);


        foreach ($provinces as $key => $prov) {
            $provinces[$key]['country_id'] = $country->id;
        }

        DB::table('provinces')->upsert($provinces, ['id'], ['id', 'name', 'rajaongkir', 'country_id']);
    }
}
