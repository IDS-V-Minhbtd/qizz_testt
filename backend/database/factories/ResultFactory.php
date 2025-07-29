<?php

namespace Database\Factories;

// database/factories/ResultFactory.php

use Illuminate\Database\Eloquent\Factories\Factory;

class ResultFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'quiz_id' => \App\Models\Quiz::factory(),
            'score' => fake()->numberBetween(0, 100),
            'time_taken' => fake()->numberBetween(30, 1800),
            'completed_at' => now(),
        ];
    }
}
