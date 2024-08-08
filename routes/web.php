<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionBatchController;
use App\Http\Controllers\ProductReturnController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomepageController::class, 'index'])->name('home.index');
Route::get('/categories/{category}/products', [HomepageController::class, 'productsByCategory'])->name('home.productsByCategory');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::group(['middleware' => ['role:admin']], function () {
        Route::prefix('admin')->group(function () {
            Route::resource('categories', CategoryController::class);
            Route::resource('products', ProductController::class);
            Route::resource('product-returns', ProductReturnController::class);
            Route::post('/products/{product}/add-stock', [ProductController::class, 'addStock'])->name('products.addStock');
            Route::resource('production-batches', ProductionBatchController::class);
            Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('admin.dashboard');
            Route::get('/dashboard/sales', [AdminDashboardController::class, 'getSalesData'])->name('admin.dashboard.sales');
            Route::resource('users', UserController::class);
        });

    });

    Route::group(['middleware' => ['role:admin|cashier']], function () {
        Route::prefix('cashier')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('cashier.cashier');
            Route::post('/orders', [OrderController::class, 'store'])->name('cashier.orders.store');
            Route::get('/orders/{order}', [OrderController::class, 'show'])->name('cashier.orders.show');
            Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('cashier.orders.invoice');
            Route::get('/home', [OrderController::class, 'home'])->name('cashier.orders.home');
            Route::post('/orders/{order}/pay', [OrderController::class, 'payOrder'])->name('cashier.orders.pay');
        });

    });

    Route::group(['middleware' => ['role:admin|cashier|customer', 'verified']], function () {
        Route::get('products/{product}/view-details', [HomepageController::class, 'productDetails'])->name('home.product.details');
        Route::get('/carts', [CartController::class, 'index'])->name('cart.index');
        Route::post('/carts', [CartController::class, 'store'])->name('cart.store');
        Route::delete('/carts/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::post('/carts/update-carts', [CartController::class, 'updateCarts'])->name('cart.updateCarts');

        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
        Route::get('/checkout/success-page', [CheckoutController::class, 'successPage'])->name('checkout.successPage');
        Route::get('/customer/orders', [ProfileController::class, 'orders'])->name('customer.orders');
        Route::get('/customer/orders/{order}', [ProfileController::class, 'showOrder'])->name('customer.orders.show');
        Route::get('/customer/orders/{order}/invoice', [ProfileController::class, 'showInvoice'])->name('customer.orders.invoice');
        Route::get('/customer/profile', [ProfileController::class, 'edit'])->name('customer.profile.edit');
        Route::put('/customer/profile/update', [ProfileController::class, 'update'])->name('customer.profile.update');

        Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    });
});

require __DIR__.'/auth.php';
