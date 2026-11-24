<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles, ?string $guard = null)
    {
        $user = Auth::guard($guard)->user();

        abort_unless($user && $user->hasRole($roles), 403, __('Not enough privilege.'));

        return $next($request);
    }
}
