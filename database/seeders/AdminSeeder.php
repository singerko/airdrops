<?php
// database/seeders/AdminSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'force_password_change' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created:');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: admin123');
        $this->command->warn('IMPORTANT: Change password on first login!');
    }
}
