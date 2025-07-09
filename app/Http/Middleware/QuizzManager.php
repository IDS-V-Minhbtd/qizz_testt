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

        // Cho phép nếu role là quizz_manager hoặc có quizz_manager_until còn hiệu lực
        if (
            $user->role !== 'quizz_manager'
            && (
                !$user->quizz_manager_until
                || (method_exists($user->quizz_manager_until, 'isFuture') ? !$user->quizz_manager_until->isFuture() : (strtotime($user->quizz_manager_until) < time()))
            )
        ) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
