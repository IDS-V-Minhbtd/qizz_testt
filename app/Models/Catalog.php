<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    protected $fillable = ['name'];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
