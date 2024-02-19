<?php

namespace Database\Seeders;

use App\Models\Departement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departements = ['IT', 'HRD', 'ACC', 'FINANCE', 'MARKETING'];
        collect($departements)->each(fn ($depart) => Departement::create(['name' => $depart]));
    }
}
