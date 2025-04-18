<?php

namespace Database\Factories;

// database/factories/QuizFactory.php

use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph,
            'time_limit' => fake()->optional()->numberBetween(1, 60),
            'is_public' => fake()->boolean(70),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}

