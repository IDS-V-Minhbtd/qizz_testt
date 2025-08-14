<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'order',
        'picture',
    ];

    // Một câu hỏi thuộc về một quiz
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Một câu hỏi có nhiều đáp án
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
