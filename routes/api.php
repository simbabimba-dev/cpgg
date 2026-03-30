<?php

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ServerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VoucherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('api.token')->group(function () {
    Route::middleware('api.ability:users.read')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });
    Route::middleware('api.ability:users.write')->group(function () {
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}', [UserController::class, 'update']);
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('users/{user}/increment', [UserController::class, 'increment'])->name('users.increment');
        Route::patch('users/{user}/decrement', [UserController::class, 'decrement'])->name('users.decrement');
        Route::patch('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        Route::patch('users/{user}/unsuspend', [UserController::class, 'unsuspend'])->name('users.unsuspend');
    });

    Route::middleware('api.ability:servers.read')->group(function () {
        Route::get('servers', [ServerController::class, 'index'])->name('servers.index');
        Route::get('servers/{server}', [ServerController::class, 'show'])->name('servers.show');
    });
    Route::middleware('api.ability:servers.write')->group(function () {
        Route::post('servers', [ServerController::class, 'store'])->name('servers.store');
        Route::put('servers/{server}', [ServerController::class, 'update'])->name('servers.update');
        Route::patch('servers/{server}', [ServerController::class, 'update']);
        Route::delete('servers/{server}', [ServerController::class, 'destroy'])->name('servers.destroy');
        Route::patch('servers/{server}/build', [ServerController::class, 'updateBuild'])->name('servers.updateBuild');
        Route::patch('servers/{server}/suspend', [ServerController::class, 'suspend'])->name('servers.suspend');
        Route::patch('servers/{server}/unsuspend', [ServerController::class, 'unSuspend'])->name('servers.unsuspend');
    });

    Route::middleware('api.ability:vouchers.read')->group(function () {
        Route::get('vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
        Route::get('vouchers/{voucher}', [VoucherController::class, 'show'])->name('vouchers.show');
    });
    Route::middleware('api.ability:vouchers.write')->group(function () {
        Route::post('vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
        Route::put('vouchers/{voucher}', [VoucherController::class, 'update'])->name('vouchers.update');
        Route::patch('vouchers/{voucher}', [VoucherController::class, 'update']);
        Route::delete('vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('vouchers.destroy');
    });

    Route::middleware('api.ability:roles.read')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    });
    Route::middleware('api.ability:roles.write')->group(function () {
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::patch('roles/{role}', [RoleController::class, 'update']);
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    Route::middleware('api.ability:products.read')->group(function () {
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    });
    Route::middleware('api.ability:products.write')->group(function () {
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('products/{product}', [ProductController::class, 'update']);
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::middleware('api.ability:notifications.read')->group(function () {
            Route::get('/{user}', [NotificationController::class, 'index'])->name('index');
            Route::get('/{user}/{notification}', [NotificationController::class, 'view'])->scopeBindings()->name('view');
        });

        Route::middleware('api.ability:notifications.write')->group(function () {
            Route::post('/send-to-users', [NotificationController::class, 'sendToUsers'])
                ->middleware('throttle:security-api-mass-notify')
                ->name('sendToUsers');
            Route::post('/send-to-all', [NotificationController::class, 'sendToAll'])
                ->middleware('throttle:security-api-mass-notify')
                ->name('sendToAll');
            Route::delete('/{user}', [NotificationController::class, 'destroyAll'])->name('destroyAll');
            Route::delete('/{user}/{notification}', [NotificationController::class, 'destroyOne'])->scopeBindings()->name('destroyOne');
        });
    });
});
