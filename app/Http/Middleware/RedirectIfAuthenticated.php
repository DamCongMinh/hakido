<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guard = $guards[0] ?? null;

        if (Auth::guard($guard)->check()) {
            $user = Auth::user();

            // Trường hợp ngoại lệ: không redirect khi đang ở forgot-password hoặc reset-password
            if ($request->is('forgot-password') || $request->is('reset-password/*')) {
                return $next($request);
            }

            // Chuyển hướng theo role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('home');
                case 'shiper':
                    return redirect()->route('shiper');
                case 'restaurant':
                    return redirect()->route('restaurant');
                default:
                    return redirect()->route('home');
            }
        }

        return $next($request);
    }

}
