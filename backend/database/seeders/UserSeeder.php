<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Quizz Master',
            'email' => 'quizzmaster@example.com',
            'password' => bcrypt('password'),
            'role' => 'quizz_master',
        ]);
    }
}