<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'result_id',
        'question_id',
        'answer_id',
        'is_correct',
    ];

    // Một user_answer thuộc về một result
    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    // Một user_answer thuộc về một question
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Một user_answer thuộc về một answer
    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }
}

