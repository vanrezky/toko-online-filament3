<?php

namespace Database\Seeders;

use App\Enums\StatusType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamps = now();
        $customers = [
            [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->safeEmail(),
                'username' => null,
                'password' => bcrypt(12345),
                'phone' => fake()->phoneNumber(),
                'balance' => 0,
                'image' => null,
                'is_active' => StatusType::ACTIVE->value,
                'created_at' => $timestamps,
                'updated_at' => $timestamps
            ],

            [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->safeEmail(),
                'username' => fake()->userName(),
                'password' => bcrypt(12345),
                'phone' => fake()->phoneNumber(),
                'balance' => 0,
                'image' => null,
                'is_active' => StatusType::ACTIVE->value,
                'created_at' => $timestamps,
                'updated_at' => $timestamps
            ]
        ];

        \App\Models\Customer::insert($customers);
    }
}
