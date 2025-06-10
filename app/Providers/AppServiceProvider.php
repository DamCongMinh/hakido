<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\Category;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Observers\OrderObserver;


use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('layout.header', function ($view) {
            $notifications = Auth::check() ? Auth::user()->notifications : collect();
            $view->with('notifications', $notifications);
        });
        Order::observe(OrderObserver::class);
    }
}
