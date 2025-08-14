<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'quizz_code',
        'time_limit',
        'is_public',
        'created_by',
        'code',
        'catalog_id',
        'lesson_id',   // thêm dòng này
        'popular',     // thêm dòng này
    ];

    // Một quiz thuộc về một catalog
    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }

    // Một quiz có nhiều câu hỏi
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // Một quiz có nhiều kết quả
    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
