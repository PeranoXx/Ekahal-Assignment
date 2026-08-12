<?php

namespace Database\Seeders;

use App\Modules\Users\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run Roles and Permissions Seeder first
        $this->call(RolesAndPermissionsSeeder::class);

        // Seed test user with User role
        $testUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $testUser->assignRole('User');
    }
}
