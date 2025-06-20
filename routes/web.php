<?php
use App\Models\User;
use App\Http\Controllers\Shipper\ShipperOrderController;
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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShowListProductController;
use App\Http\Controllers\SearchAndFilterController;
use App\Http\Controllers\SearchRestaurantController;
use App\Http\Controllers\ShowDetailController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VnpayController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Restaurant\RestaurantProductController;
use App\Http\Controllers\Restaurant\RestaurantStatisticsController;

// Giao diện chính (FE)

Route::get('/', [HomeController::class, 'index'])->name('home');

// hiển thị danh sách sản phẩm trong trang list_product
Route::get('/products/category/{category_id}', [ShowListProductController::class, 'byCategory'])->name('products.byCategory');

// lọc sản phẩm
Route::get('/products', [SearchAndFilterController::class, 'index'])->name('products.index');
Route::get('/products/filter', [SearchAndFilterController::class, 'filter'])->name('products.filter');

// tim kiem
Route::get('/search', [SearchAndFilterController::class, 'search']);
Route::get('/search-suggestions', [SearchAndFilterController::class, 'suggestions']);
// Route::get('/search-restaurant', [SearchRestaurantController::class, 'search'])->name('search.restaurant');

//show detail product
Route::get('/product/{type}/{id}', [ShowDetailController::class, 'show'])->name('product.show');

// listrestaurant
Route::get('/search', [SearchAndFilterController::class, 'search'])->name('search');


// Route::get('/list-product', fn () => view('web.list-product'))->name('list-product');
Route::get('/detail', fn () => view('web.detail_product'))->name('detail');

// Route trang Restaurant (đúng Controller, đúng dữ liệu)
Route::middleware(['auth'])->group(function () {
    Route::get('/restaurant', [RestaurantStatisticsController::class, 'index'])->name('restaurant');
    Route::get('/shiper', fn () => view('shiper.shiper'))->name('shiper');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin/accounts')->name('admin.accounts.')->group(function () {
    Route::get('/', [AdminAccountController::class, 'index'])->name('index');
    
});

Route::get('/home', [HomeController::class, 'index'])->name('home');


Route::get('/home_admin', fn () => view('admin.home_admin'))
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard');

// Hiển thị giao diện đăng nhập + đăng ký (gộp chung 1 view)
Route::get('/login', function () {
    return view('web.login'); 
})->name('login');

// hiển thị giao diện quản lý sản phẩm
Route::get('/control_product', [ProductController::class, 'index'])->name('control_product')->middleware(['auth', 'admin']);

// Xử lý đăng nhập
Route::post('/login', [AuthController::class, 'login'])->name('postlogin');
// Route::post('/login', function(){ echo 'hchdfhdc';})->name('postlogin');

// Xử lý đăng ký
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('login/facebook', [FacebookController::class, 'redirectToFacebook'])->name('facebook.redirect');
Route::get('login/facebook/callback', [FacebookController::class, 'handleFacebookCallback'])->name('facebook.callback');

Route::get('login/google', [GoogleController::class, 'redirect']);
Route::get('login/google/callback', [GoogleController::class, 'callback']);

//lưu voucher
Route::post('/remember-voucher', function (Request $request) {
    session(['voucher_code' => $request->code]);
    return response()->json(['message' => 'Voucher saved']);
});
Route::post('/apply-voucher', [CartController::class, 'applyVoucher'])->name('checkout.applyVoucher');

//customer
Route::middleware(['auth', 'role:customer'])->group(function () {
    // Giỏ hàng
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/detail/add', [ShowDetailController::class, 'add'])->name('detail.add');
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::get('/cart/checkout', [CartController::class, 'showCheckout'])->name('cart.checkout');
    Route::post('/cart/checkout', [CartController::class, 'processCheckout'])->name('cart.processCheckout'); 
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::delete('/cart/remove', [CartController::class, 'removeItem'])->name('cart.removeItem');

    // Đặt hàng
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/order/success', [OrderController::class, 'success'])->name('order.success');
    Route::get('/ordered-items', [OrderController::class, 'orderedItems'])->name('orders.items');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::match(['get', 'post'], '/checkout-now', [ShowDetailController::class, 'processCheckout'])->name('checkout.now');
    Route::get('/checkout', [ShowDetailController::class, 'checkout'])->name('checkout');




    // show view và lấy order_id
    Route::get('/review/{orderId}', [ReviewController::class, 'showOrderReviewForm'])
    ->name('reviews.reviews');

    // gửi thông tin đánh giá
    Route::post('/review/food', [ReviewController::class, 'FoodReview'])
    ->name('reviews.FoodReview');
    Route::post('/review/beverage', [ReviewController::class, 'BeverageReview'])
    ->name('reviews.BeverageReview');
    Route::post('/reviews/shipping', [ReviewController::class, 'ShippingReview'])->name('reviews.ShippingReview');
        
});

    Route::post('/vnpay/payment', [VnpayController::class, 'PaymentVnpay'])->name('vnpay.payment');
    Route::get('/vnpay/return', [VnpayController::class, 'vnpayReturn']);
    Route::match(['GET', 'POST'], '/vnpay/ipn', [VnpayController::class, 'vnpay_ipn']);
    Route::post('/vnpay/ipn', [VnpayController::class, 'handleIpn']);



// Trang thông tin cá nhân
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'home_info'])->name('profile.home_info');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route đổi mật khẩu
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change_password_form');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change_password');

    // Route::post('/orders/{order}/update-status', [NotificationController::class, 'updateOrderStatus'])->name('notification');
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch']);
    Route::get('/notifications/read/{id}', function ($id) {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['url'] ?? '/');
    })->name('notifications.read');
    

});

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

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Quản lý tài khoản
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [AdminAccountController::class, 'account'])->name('index');
        Route::post('/toggle/{id}', [AdminAccountController::class, 'toggleActive'])->name('toggle');
        Route::delete('/delete/{id}', [AdminAccountController::class, 'destroy'])->name('delete');
        Route::post('/{id}/approve', [AdminAccountController::class, 'approveUser'])->name('approve');
    });

    // Quản lý Đồ Ăn
    Route::prefix('foods')->name('foods.')->group(function () {
        Route::get('/', [FoodController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [FoodController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [FoodController::class, 'update'])->name('update');
        Route::post('/approve/{id}', [FoodController::class, 'approve'])->name('approve');
        Route::delete('/destroy/{id}', [FoodController::class, 'destroy'])->name('destroy');
        Route::post('/reject/{id}', [FoodController::class, 'reject'])->name('reject');
        
    });

    // Quản lý Đồ Uống
    Route::prefix('beverages')->name('beverages.')->group(function () {
        Route::get('/', [BeverageController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [BeverageController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [BeverageController::class, 'update'])->name('update');
        Route::post('/approve/{id}', [BeverageController::class, 'approve'])->name('approve');
        Route::delete('/destroy/{id}', [BeverageController::class, 'destroy'])->name('destroy');
        Route::post('/reject/{id}', [BeverageController::class, 'reject'])->name('reject');
    });

    // Quản lý Đặt Hàng
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::post('/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{order}/assign-shipper', [AdminOrderController::class, 'assignShipper'])->name('assignShipper');
        Route::post('/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('cancel');
    });

    // Quản trị nội dung
    Route::get('/content', [ContentManagementController::class, 'index'])->name('content');

    // Slides & Categories
    Route::resource('slides', SlideController::class);
    Route::resource('categories', CategoryController::class);

    // Thống kê
    Route::get('/home', [AdminStatisticController::class, 'showHomeStatistics'])->name('home_statistics');
    Route::get('/statistics', [AdminStatisticController::class, 'revenueStatistics'])->name('statistics');
    Route::get('/orderstatistics', [AdminStatisticController::class, 'orderStatistics'])->name('orderstatistics');
    Route::get('/inventoryStatistics', [AdminStatisticController::class, 'inventoryStatistics'])->name('inventoryStatistics');
});

 // Restaurant quản lý sản phẩm và thống kê doanh thu

 Route::middleware(['auth', 'role:restaurant'])->prefix('restaurant')->name('restaurant.')->group(function () {
    Route::get('/products', [RestaurantProductController::class, 'home'])->name('products.home');
    Route::get('/products/create', [RestaurantProductController::class, 'create'])->name('products.create');
    Route::post('/products', [RestaurantProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [RestaurantProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [RestaurantProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [RestaurantProductController::class, 'destroy'])->name('products.destroy');
    
    Route::get('/create/vouchers', [VoucherController::class, 'createVoucher'])->name('create.voucher');
    Route::post('/add/vouchers', [VoucherController::class, 'addVoucher'])->name('add.voucher');
    Route::get('/vouchers/home', [VoucherController::class, 'homeVoucher'])->name('home.voucher');
    Route::get('/detail/{type}/{id}', [VoucherController::class, 'showDetailWithVouchers'])->name('detail.voucher');

    //thống kê và quản lý đơn hàng
    Route::get('/statistics', [RestaurantStatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/statistics/home', [RestaurantStatisticsController::class, 'home'])->name('statistics.home');
    Route::post('/orders/{id}/approve', [RestaurantStatisticsController::class, 'approveOrder'])->name('order.approve');
    Route::post('/orders/{id}/cancel', [RestaurantStatisticsController::class, 'cancelOrder'])->name('order.cancel');
    
});

// shipper
Route::prefix('shipper/orders')->name('shipper.orders.')->middleware(['auth'])->group(function () {
    Route::get('/available', [ShipperOrderController::class, 'availableOrders'])->name('available');
    Route::post('/accept/{id}', [ShipperOrderController::class, 'acceptOrder'])->name('accept');
    Route::get('/current', [ShipperOrderController::class, 'currentDelivery'])->name('current');
    Route::post('/update-status/{id}', [ShipperOrderController::class, 'updateStatus'])->name('updateStatus');
    Route::get('/history', [ShipperOrderController::class, 'deliveryHistory'])->name('history');
    Route::get('/income-stats', [ShipperOrderController::class, 'incomeStats'])->name('incomeStats');
    Route::get('/shipper/income-stats', [ShipperOrderController::class, 'incomeStats'])->name('shipper.income.stats');
});


Route::get('/test', function () {
    return "<script>alert('hehe');</script>";
});

Route::get('/homepage', function () {
    return 'trang home';
});