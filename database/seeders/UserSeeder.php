<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => 'admin123',
                'is_admin' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'student1@student.com'],
            [
                'name' => 'Student One',
                'password' => 'student123',
                'is_admin' => false,
            ]
        );
    }
}