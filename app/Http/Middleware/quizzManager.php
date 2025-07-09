<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QuizzManager
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
       $user = auth()->user();

if (!auth()->check()) {
    abort(403, 'Bạn chưa đăng nhập.');
}

// Cho phép nếu role là quizz_manager hoặc nếu có thời hạn còn hiệu lực
if ($user->role !== 'quizz_manager' && !($user->quizz_manager_until && $user->quizz_manager_until->isFuture())) {
    abort(403, 'Bạn không có quyền truy cập.');
}

    }
}
