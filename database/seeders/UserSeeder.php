<?php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@hospital.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole && !$admin->roles->contains($adminRole)) {
            $admin->roles()->attach($adminRole);
        }

        // Create Staff User
        $staff = User::firstOrCreate(
            ['email' => 'staff@hospital.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $staffRole = Role::where('slug', 'staff')->first();
        if ($staffRole && !$staff->roles->contains($staffRole)) {
            $staff->roles()->attach($staffRole);
        }
    }
}