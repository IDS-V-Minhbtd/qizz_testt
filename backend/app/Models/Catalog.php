<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Catalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        // Nếu có thêm trường khác như description thì thêm vào đây
    ];

    // Một catalog có nhiều quiz
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
