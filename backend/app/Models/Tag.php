<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Nếu tag dùng cho course, bạn có thể thêm quan hệ sau:
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
