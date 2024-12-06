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
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Resources\TopSalesResource;
use App\Models\OrderItems;
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
            Route::post('products/{product}/restore', action: [ProductController::class, 'restore'])->withTrashed()->name('products.restore');
            Route::get('products/archive', [ProductController::class, 'archive'])->name('products.archive');
            Route::resource('products', ProductController::class);

            Route::resource('product-returns', ProductReturnController::class);
            Route::resource('stocks', StockController::class);
            Route::post('/stocks/{stock}/add-stock', [StockController::class, 'addStock'])->name('stocks.addStock');
            Route::post('/products/{product}/add-stock', [ProductController::class, 'addStock'])->name('products.addStock');
            Route::resource('production-batches', ProductionBatchController::class);
            Route::get('/dashboard/print-pdf', [AdminDashboardController::class, 'printPdf'])->name('admin.dashboard.print-pdf');
            Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('admin.dashboard');
            Route::get('/dashboard/sales', [AdminDashboardController::class, 'getSalesData'])->name('admin.dashboard.sales');
            Route::get('/dashboard/sales/export', [AdminDashboardController::class, 'getOrderItems'])->name('admin.dashboard.order-items.export');
            Route::get('/users/print-pdf', [UserController::class, 'printPdf'])->name('users.print-pdf');
            Route::resource('users', UserController::class);
        });

    });

    Route::group(['middleware' => ['role:admin|cashier']], function () {
        Route::prefix('cashier')->group(function () {

            Route::get('/', [OrderController::class, 'index'])->name('cashier.cashier');
            Route::get('/orders/print-pdf', [OrderController::class, 'printPdf'])->name('cashier.orders.print-pdf');
            Route::post('/orders', [OrderController::class, 'store'])->name('cashier.orders.store');
            Route::get('/orders/{order}', [OrderController::class, 'show'])->name('cashier.orders.show');
            Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('cashier.orders.invoice');
            Route::get('/orders/{order}/invoice/pdf', [OrderController::class, 'showInvoice'])->name('cashier.orders.invoice.pdf');

            Route::get('/home', [OrderController::class, 'home'])->name('cashier.orders.home');
            Route::post('/orders/{order}/pay', [OrderController::class, 'payOrder'])->name('cashier.orders.pay');
            Route::post('/orders/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('cashier.orders.cancel');
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
        Route::post('/customer/orders/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('customer.orders.cancel');
        Route::get('/customer/orders/{order}', [ProfileController::class, 'showOrder'])->name('customer.orders.show');
        Route::get('/customer/orders/{order}/invoice', [ProfileController::class, 'showInvoice'])->name('customer.orders.invoice');
        Route::get('/customer/profile', [ProfileController::class, 'edit'])->name('customer.profile.edit');
        Route::put('/customer/profile/update', [ProfileController::class, 'update'])->name('customer.profile.update');
        Route::get('/customer/orders/{order}/invoice/pdf', [OrderController::class, 'showInvoice'])->name('customer.orders.invoice.pdf');

        Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    });
});

Route::get('/test', function () {
    $topSales = OrderItems::query()
        ->whereHas('order', function ($query) {
            $query->where('status', 'paid');
        })
        ->with('product')
        ->selectRaw('SUM(quantity) as total_quantity, SUM(total_price) as total_price, product_id')
        ->groupBy('product_id')
        ->orderBy('total_quantity', 'desc')
        ->limit(5)
        ->get();

    return TopSalesResource::collection($topSales);
});

require __DIR__.'/auth.php';
