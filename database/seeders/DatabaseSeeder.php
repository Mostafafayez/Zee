<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

public function run(): void
{
    DB::table('users')->insert([
        'name' => 'Admin User',
        'email' => 'admin@gmail.com',
        'phone' => '010000200000',
        'address' => 'Admin Address',
        'role' => 'admin',
        'password' => Hash::make('12345678'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

      $this->call([
        UserAndRolesSeeder::class,
    ]);
}
}
