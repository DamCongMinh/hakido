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
            $user = Auth::user();

            $notifications = $user ? $user->notifications : collect();
            $cartItemCount = 0;

            if ($user && $user->cart) {
                $cartItemCount = $user->cart->items()->sum('quantity');
            }

            $view->with([
                'notifications' => $notifications,
                'cartItemCount' => $cartItemCount,
            ]);
        });

        Order::observe(OrderObserver::class);
    }
}
