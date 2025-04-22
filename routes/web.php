<?php
use App\Models\User;
use App\Http\Controllers\Admin\AdminStatisticController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentManagementController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\FoodController;
use App\Http\Controllers\Admin\BeverageController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\FacebookController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\Restaurant\RestaurantProductController;
use App\Http\Controllers\Restaurant\RestaurantStatisticsController;
// use App\Http\Controllers\Restaurant\RestaurantProfileController;
// Giao diện chính (FE)

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/list-product', fn () => view('web.list-product'))->name('list-product');
Route::get('/detail', fn () => view('web.detail_product'))->name('detail');

// Trang theo role
// Route trang Restaurant (đúng Controller, đúng dữ liệu)
Route::middleware(['auth'])->group(function () {
    Route::get('/restaurant', [RestaurantStatisticsController::class, 'index'])->name('restaurant');
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

// load image
Route::post('/update/{id}', [FoodController::class, 'update'])->name('foods.update');



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

// Quản trị nội dung
Route::get('/admin/content', [ContentManagementController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.content');
Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category.show');
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('slides', SlideController::class);
    Route::resource('categories', CategoryController::class);
});
    
//Thống kê doanh thu và số lượng đơn hàng
Route::get('/admin/statistics', [AdminStatisticController::class, 'index'])->name('admin.statistics');


 // Restaurant quản lý sản phẩm và thống kê doanh thu

 Route::middleware(['auth', 'role:restaurant'])->prefix('restaurant')->name('restaurant.')->group(function () {
    Route::get('/products', [RestaurantProductController::class, 'home'])->name('products.home');
    Route::get('/products/create', [RestaurantProductController::class, 'create'])->name('products.create');
    Route::post('/products', [RestaurantProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [RestaurantProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [RestaurantProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [RestaurantProductController::class, 'destroy'])->name('products.destroy');

    //thống kê
    Route::get('/statistics', [RestaurantStatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/statistics/home', [RestaurantStatisticsController::class, 'home'])->name('statistics.home');

    //tạo hồ sơ nhà hàng
    // Route::get('/create', [RestaurantProfileController::class, 'create'])->name('create');
    // Route::post('/store', [RestaurantProfileController::class, 'store'])->name('store');
    // Route::get('/dashboard', [RestaurantProfileController::class, 'dashboard'])->name('dashboard');

});

// Route kiểm tra hồ sơ nhà hàng và redirect phù hợp
// Route::middleware(['auth', 'role:restaurant'])->get('/restaurant', function () {
//     $user = Auth::user();
//     return $user->restaurant
//         ? redirect()->route('restaurant.dashboard')
//         : redirect()->route('restaurant.create');
// })->name('restaurant.redirect');
   
    
