<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'real_name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'edit_count' => 0,
            ]
        );

        // Create 10 regular users
        for ($i = 1; $i <= 10; $i++) {
            User::updateOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "User {$i}",
                    'real_name' => "Regular User {$i}",
                    'email' => "user{$i}@example.com",
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'email_verified_at' => now(),
                    'edit_count' => 0,
                ]
            );
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Users: user1@example.com to user10@example.com / password');
    }
}
