<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'name' => 'Admin',
            'username' => 'admin',
        ]);
        $su = User::create([
            'email' => 'superadmin@example.com',
            'password' => bcrypt('admin123'),
            'name' => 'Admin',
            'username' => 'superadmin',
            'is_super_user' => 1
        ]);

        Artisan::call('shield:super-admin', [
            '--user' => $su->id
        ]);

        User::factory(5)->create();
    }
}
