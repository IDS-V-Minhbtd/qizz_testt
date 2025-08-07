<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class field extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tag_id',
    ];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
