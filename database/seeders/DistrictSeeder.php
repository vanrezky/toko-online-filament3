<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $fileContent = Storage::get('locations/indonesia/districts.json');
        $districts = collect(json_decode($fileContent, true))->chunk(40);

        foreach ($districts as $key => $dis) {
            DB::table('districts')->upsert($dis->toArray(), ['id'], ['id', 'province_id', 'type', 'name', 'rajaongkir', 'postal_code']);
        }
    }
}
