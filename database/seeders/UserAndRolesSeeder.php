<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User; // adjust namespace if you're not using default
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserAndRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $roles = ['admin', 'courier', 'merchant'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'phone' => '01000000000',
                'address' => 'Admin Address',
                'password' => Hash::make('12345678'),
            ]
        );

        // Assign Role
        $admin->assignRole('admin');
    }
}
