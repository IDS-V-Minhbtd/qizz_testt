<?php

namespace Database\Factories;

// database/factories/AnswerFactory.php

use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question_id' => \App\Models\Question::factory(),
            'answer' => fake()->sentence(5),
            'is_correct' => fake()->boolean(25), // xác suất 25% đúng
        ];
    }
}
