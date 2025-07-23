<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        Quiz::create([
            'lesson_id' => 1,
            'name' => 'Quiz Laravel Cơ Bản',
            'description' => 'Quiz kiểm tra kiến thức Laravel cơ bản.',
            'time_limit' => 900,
            'is_public' => true,
            'created_by' => 1,
            'popular' => false,
        ]);
    }
} 