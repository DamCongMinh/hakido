<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UpdateLastActiveAt
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'restaurant' && $user->restaurant) {
                $user->restaurant->update(['last_active_at' => now()]);
            }
        }

        return $next($request);
    }
}

