<?php
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\FoodController;
use App\Http\Controllers\Admin\BeverageController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\FacebookController;
use App\Http\Controllers\Auth\GoogleController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Laravel\Socialite\Facades\Socialite;

// Giao diện chính (FE)
Route::get('/', fn () => view('web.home'))->name('home');
Route::get('/list-product', fn () => view('web.list-product'))->name('list-product');
Route::get('/detail', fn () => view('web.detail_product'))->name('detail');

// Trang theo role
Route::middleware(['auth'])->group(function () {
    Route::get('/restaurant', fn () => view('restaurant.restaurant'))->name('restaurant');
    Route::get('/shiper', fn () => view('shiper.shiper'))->name('shiper');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin/accounts')->name('admin.accounts.')->group(function () {
    Route::get('/', [AdminAccountController::class, 'index'])->name('index');
    // Các route khác của admin...
});
Route::get('/home_admin', fn () => view('admin.home_admin'))->name('admin.dashboard');


// Hiển thị giao diện đăng nhập + đăng ký (gộp chung 1 view)
Route::get('/login', function () {
    return view('web.login'); 
})->name('login');

// hiển thị giao diện quản lý sản phẩm
Route::get('/control_product', [ProductController::class, 'index'])->name('control_product')->middleware(['auth', 'admin']);


// Xử lý đăng nhập
Route::post('/login', [AuthController::class, 'login'])->name('postlogin');

// Xử lý đăng ký
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('login/facebook', [FacebookController::class, 'redirect']);
Route::get('login/facebook/callback', [FacebookController::class, 'callback']);

Route::get('login/google', [GoogleController::class, 'redirect']);
Route::get('login/google/callback', [GoogleController::class, 'callback']);


// Quên mật khẩu

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');

// Quản lý tài khoản
Route::middleware(['auth', 'admin'])->prefix('admin/accounts')->name('admin.accounts.')->group(function () {
    Route::get('/', [AdminAccountController::class, 'index'])->name('index');
    
    Route::post('/approve/{id}', [AdminAccountController::class, 'approve'])->name('approve');
    Route::post('/toggle/{id}', [AdminAccountController::class, 'toggleActive'])->name('toggle');
    Route::delete('/delete/{id}', [AdminAccountController::class, 'destroy'])->name('delete');
});

// Quản lý Đồ Ăn
Route::middleware(['auth', 'admin'])->prefix('admin/foods')->name('foods.')->group(function () {
    Route::get('/', [FoodController::class, 'index'])->name('index');
    Route::get('/edit/{id}', [FoodController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [FoodController::class, 'update'])->name('update');
    Route::post('/approve/{id}', [FoodController::class, 'approve'])->name('approve');
    Route::delete('/destroy/{id}', [FoodController::class, 'destroy'])->name('destroy');
});

// Quản lý Đồ Uống
Route::middleware(['auth', 'admin'])->prefix('admin/beverages')->name('beverages.')->group(function () {
    Route::get('/', [BeverageController::class, 'index'])->name('index');
    Route::get('/edit/{id}', [BeverageController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [BeverageController::class, 'update'])->name('update');
    Route::post('/approve/{id}', [BeverageController::class, 'approve'])->name('approve');
    Route::delete('/destroy/{id}', [BeverageController::class, 'destroy'])->name('destroy');
});

// Quản lý Đặt Hàng
Route::middleware(['auth', 'admin'])->prefix('admin/orders')->name('admin.orders.')->group(function () {
    Route::get('/', [AdminOrderController::class, 'index'])->name('index');
    Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
    Route::post('/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/{order}/assign-shipper', [AdminOrderController::class, 'assignShipper'])->name('assignShipper');
    Route::post('/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('cancel');
});

