<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'name' => 'Admin',
            'username' => 'admin',
            'is_super_user' => 1
        ]);

        User::factory(5)->create();
    }
}
