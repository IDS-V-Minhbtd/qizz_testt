<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'answer',
        'is_correct',
    ];

    // Một đáp án thuộc về một câu hỏi
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
