<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            abort(403);
        }

        $user = Auth::user();

        if (!$user->role || !in_array($user->role->slug, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
