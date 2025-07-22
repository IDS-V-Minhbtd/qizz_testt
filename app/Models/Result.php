<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'time_taken',
        'completed_at',
    ];

    // Một kết quả thuộc về một user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Một kết quả thuộc về một quiz
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Một kết quả có nhiều user_answers
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
