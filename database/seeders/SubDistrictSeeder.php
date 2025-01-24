<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubDistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileContent = Storage::get('locations/indonesia/subdistricts.json');
        $subDistricts = collect(json_decode($fileContent, true))->chunk(40);

        foreach ($subDistricts as $key => $subs) {
            DB::table('sub_districts')->upsert($subs->toArray(), ['id'], ['id', 'district_id', 'name', 'rajaongkir', 'postal_code']);
        }
    }
}
