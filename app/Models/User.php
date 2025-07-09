<?php

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'created_by');
    }

    public function results()
    {
        return $this->hasMany(\App\Models\Result::class, 'user_id');
    }
///role

    public function isAdmin(): bool
    {
        return $this->role === RoleEnum::ADMIN->value;
    }

    public function isQuizzManager(): bool
    {
        return $this->role === RoleEnum::QUIZZ_MANAGER->value;
    }

    public function isUser(): bool
    {
        return $this->role === RoleEnum::USER->value;
    }
}
