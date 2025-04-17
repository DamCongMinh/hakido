<?php
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\FacebookController;
use App\Http\Controllers\Auth\GoogleController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

//các route fe
Route::get('login', function () {
    return view('web.login');
})->name('login.page');

Route::get('home', function () {
    return view('web.home');
})->name('home');

Route::get('list-product', function () {
    return view('web.list-product');
})->name('list-product');

Route::get('detail', function () {
    return view('web.detail_product');
})->name('detail');

Route::get('/restaurant', function () {
    return view('restaurant.restaurant');
})->name('restaurant');

Route::get('/shiper', function () {
    return view('shiper.shiper');
})->name('shiper');

Route::get('/home_admin', function () {
    return view('admin.home_admin');
})->name('admin.dashboard');



Route::view('/privacy-policy', 'privacy-policy');
Route::view('/delete-data', 'delete-data');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/login', [AuthController::class, 'login'])->name('postlogin');


// Bắt đầu đăng nhập Google

Route::get('/login/google', [GoogleController::class, 'redirect']);
Route::get('/login/google/callback', [GoogleController::class, 'callback']);

// Bắt đầu đăng nhập facebook
Route::get('/login/facebook', [FacebookController::class, 'redirectToFacebook']);
Route::get('/login/facebook/callback', [FacebookController::class, 'handleFacebookCallback']);

// Trang quên mật khẩu
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('guest')->name('password.email');

// Trang reset mật khẩu
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('guest')->name('password.update');


Route::get('/admin/accounts', [AdminAccountController::class, 'index'])->name('admin.accounts');

