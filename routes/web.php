<?php

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
// Route::get('/login', [AuthController::class, 'login'])->name('login');
