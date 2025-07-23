<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description',
        'created_by',
        'tag_id',
        'image',
        'slug',
        'is_public',
    ];

    // Người tạo khóa học
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Khóa học có nhiều bài học
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    // Khóa học có nhiều tiến độ của người dùng
    public function progresses()
    {
        return $this->hasMany(Progress::class);
    }

    // Thêm quan hệ với Tag
    public function tag()
    {
        return $this->belongsTo(\App\Models\Tag::class, 'tag_id');
    }
}
