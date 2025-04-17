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

        // Kiểm tra nếu người dùng đã đăng nhập, sẽ chuyển hướng về home hoặc trang nào đó
        if (Auth::guard($guard)->check()) {
            // Nếu người dùng đã đăng nhập và truy cập vào trang quên mật khẩu hoặc reset mật khẩu, không chuyển hướng
            if ($request->is('forgot-password') || $request->is('reset-password/*')) {
                return $next($request);
            }

            // Nếu không phải là trang quên mật khẩu hoặc reset mật khẩu, chuyển hướng về home
            return redirect('/');
        }

        return $next($request);
    }
}
