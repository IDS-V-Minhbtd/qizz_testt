<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $userRole = auth()->user()->role ?? null;

        // Debug log to check the user's role
        \Log::debug('Checking user role in IsAdmin middleware', ['user_role' => $userRole, 'required_roles' => $roles]);

        if (!auth()->check() || !in_array($userRole, $roles)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
