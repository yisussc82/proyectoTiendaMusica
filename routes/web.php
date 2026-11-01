<?php

use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Panel\OrderController as PanelOrderController;
use App\Http\Controllers\Shop\CatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/products', [CatalogController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [CatalogController::class, 'show'])->name('products.show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('role:customer')
        ->prefix('account')
        ->name('account.')
        ->group(function () {
            Route::view('/', 'account.dashboard')->name('dashboard');
            Route::resource('addresses', AddressController::class)->except('show');
            Route::get('/orders', [AccountOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [AccountOrderController::class, 'show'])->name('orders.show');
            Route::post('/orders/{order}/cancel', [AccountOrderController::class, 'cancel'])->name('orders.cancel');
        });

    Route::middleware('role:admin,catalog,warehouse,support')
        ->prefix('panel')
        ->name('panel.')
        ->group(function () {
            Route::view('/', 'panel.dashboard')->name('dashboard');
            Route::get('/orders', [PanelOrderController::class, 'index'])
                ->middleware('role:admin,warehouse,support')
                ->name('orders.index');
        });
});
