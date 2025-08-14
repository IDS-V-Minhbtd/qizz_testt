<?php

namespace Database\Factories;

// database/factories/UserAnswerFactory.php

use Illuminate\Database\Eloquent\Factories\Factory;

class UserAnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'result_id' => \App\Models\Result::factory(),
            'question_id' => \App\Models\Question::factory(),
            'answer' => fake()->sentence(5),
            'is_correct' => fake()->boolean(50),
        ];
    }
}
