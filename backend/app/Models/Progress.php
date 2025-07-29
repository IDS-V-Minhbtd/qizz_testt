<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'progress_percent',
        'last_viewed_at',
    ];

    // Mỗi tiến độ thuộc về một người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Mỗi tiến độ có thể liên kết với một khóa học
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Mỗi tiến độ có thể liên kết với một bài học
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
