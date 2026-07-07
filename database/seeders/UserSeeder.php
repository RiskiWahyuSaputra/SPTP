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
        $password = Hash::make('password');

        $users = [
            ['name' => 'Staff User', 'email' => 'staff@test.com', 'role_slug' => 'staff'],
            ['name' => 'SPV User', 'email' => 'spv@test.com', 'role_slug' => 'spv'],
            ['name' => 'Manager User', 'email' => 'manager@test.com', 'role_slug' => 'manager'],
            ['name' => 'Direktur User', 'email' => 'direktur@test.com', 'role_slug' => 'direktur'],
            ['name' => 'Finance User', 'email' => 'finance@test.com', 'role_slug' => 'finance'],
        ];

        foreach ($users as $user) {
            $role = Role::where('slug', $user['role_slug'])->firstOrFail();

            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $password,
                'role_id' => $role->id,
                'is_active' => true,
            ]);
        }
    }
}
