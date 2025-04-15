<?php
use App\Http\Controllers\Auth\GoogleController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

//các route fe
Route::get('login', function () {
    return view('web.login');
})->name('login');

Route::get('home', function () {
    return view('web.home');
})->name('home');

Route::get('list-product', function () {
    return view('web.list-product');
})->name('list-product');

Route::get('detail', function () {
    return view('web.detail_product');
})->name('detail');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/login', [AuthController::class, 'login'])->name('postlogin');


// Bắt đầu đăng nhập Google

Route::get('/login/google', [GoogleController::class, 'redirect']);
Route::get('/login/google/callback', [GoogleController::class, 'callback']);


