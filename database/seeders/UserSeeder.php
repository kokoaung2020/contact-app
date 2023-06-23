<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make("admin123")
        ]);

        User::factory()->create([
            'name' => 'User One',
            'email' => 'userone@gmail.com',
            'password' => Hash::make("user123")
        ]);

        User::factory()->create([
            'name' => 'User Two',
            'email' => 'user@gmail.com',
            'password' => Hash::make("user123")
        ]);
    }
}
