<?php

namespace Database\Seeders;

use App\Models\DistributorLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistributorLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distributorLevels = [
            [
                'name' => 'Reseller',
                'description' => 'Reseller description',
                'level' => 1
            ],
            [
                'name' => 'Agent',
                'description' => 'Agent description',
                'level' => 2
            ],
            [
                'name' => 'Agent Premium',
                'description' => 'Agent Premium description',
                'level' => 3
            ],
            [
                'name' => 'Distributor',
                'description' => 'Distributor description',
                'level' => 4
            ],
        ];

        foreach ($distributorLevels as $key => $level) {
            DistributorLevel::create($level);
        }
    }
}
