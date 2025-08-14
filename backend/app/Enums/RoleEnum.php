<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case QUIZZ_MANAGER = 'quizz_manager';
    case USER = 'user';
}
