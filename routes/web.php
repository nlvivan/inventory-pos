<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionBatchController;
use App\Http\Controllers\ProductReturnController;
use App\Http\Controllers\ProfileController;
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
            Route::get('/dashboard', function () {
                return Inertia::render('Dashboard');
            })->middleware(['auth', 'verified'])->name('dashboard');
        });

    });

    Route::group(['middleware' => ['role:admin|cashier']], function () {
        Route::prefix('cashier')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('cashier.cashier');
            Route::post('/orders', [OrderController::class, 'store'])->name('cashier.orders.store');
            Route::get('/orders/{order}', [OrderController::class, 'show'])->name('cashier.orders.show');
            Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('cashier.orders.invoice');
            Route::get('/home', [OrderController::class, 'home'])->name('cashier.orders.home');
        });

    });
});

require __DIR__.'/auth.php';
