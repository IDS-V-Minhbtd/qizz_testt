<?php

namespace Database\Factories;

// database/factories/QuestionFactory.php

use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quiz_id' => \App\Models\Quiz::factory(),
            'question' => fake()->sentence(10),
            'order' => fake()->numberBetween(1, 10),
        ];
    }
}

