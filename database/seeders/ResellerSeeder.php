<?php

namespace Database\Seeders;

use App\Models\Reseller;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resellers = [
            [
                'name' => 'Reseller',
                'description' => 'Is the reseller level',
                'level' => 1
            ],
            [
                'name' => 'Agent',
                'description' => 'is the agent level',
                'level' => 2
            ],
            [
                'name' => 'Agent Premium',
                'description' => 'Is the agent premium',
                'level' => 3
            ],
            [
                'name' => 'Distributor',
                'description' => 'This is distributor',
                'level' => 4
            ],
        ];

        foreach ($resellers as $key => $level) {
            Reseller::create($level);
        }
    }
}
