<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name', 'description', 'image', 'created_by', 'level', 'target', 'requirement', 'field_id', 'status'
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'course_tag');
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}