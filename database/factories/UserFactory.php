<?php

namespace Database\Factories;

// database/factories/UserFactory.php

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'email' => fake()->unique()->safeEmail,
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin()
    {
        return $this->state(fn () => ['role' => 'admin']);
    }
}

