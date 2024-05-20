<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UsersRole;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'first_name' => 'Test User',
        //     'last_name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        UsersRole::factory()->createMany([
            [
                'role_type' => 'admin',
                'role_name' => 'Admin',
            ],
            [
                'role_type' => 'user',
                'role_name' => 'User',
            ],
        ]);
    }
}
