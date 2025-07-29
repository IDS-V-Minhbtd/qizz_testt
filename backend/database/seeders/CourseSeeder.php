<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::create([
            'name' => 'Laravel Cơ Bản',
            'description' => 'Khóa học nhập môn Laravel.',
            'created_by' => 1,
            'tag_id' => 1,
            'slug' => 'laravel-co-ban',
            'image' => null,
        ]);
    }
} 