<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = ['lesson_id', 'front', 'back'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function Course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
