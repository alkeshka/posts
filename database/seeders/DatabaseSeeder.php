<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UsersRole;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $adminRole = UsersRole::create([
            'role_type' => 'admin',
            'role_name' => 'Admin',
        ]);

        UsersRole::create([
            'role_type' => 'user',
            'role_name' => 'User',
        ]);

        User::factory()->create([
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => '',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'users_role_type_id' => $adminRole->id,
        ]);
    }
}
