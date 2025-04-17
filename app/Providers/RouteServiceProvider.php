<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        // Gọi boot() của lớp cha
        parent::boot();

        // Route FE
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        // Route Admin
        // Route::middleware(['web', 'auth', 'admin']) // middleware tùy chỉnh
        //     ->prefix('admin')                       // URL bắt đầu bằng /admin
        //     ->as('admin.')                          // tên route bắt đầu bằng admin.
        //     ->group(base_path('routes/admin.php'));
    }
}
