<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryAuditController;
use App\Http\Controllers\StampTransactionController;
use App\Http\Controllers\StockController;


Route::middleware(['auth'])->group(function () {
    Route::redirect('/', '/dashboard');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

// ゲスト（未ログイン）のみアクセス可能なルート
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');


// URL: /stamps/...   ルート名: stamps.*
Route::middleware('auth')->prefix('stamps')->name('stamps.')->group(function () {
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');

    Route::resource('transactions', StampTransactionController::class)
        ->only(['index', 'create', 'store']);

    // 管理者（users.auth_type = 0）のみ
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('audits', InventoryAuditController::class)
            ->only(['index', 'create', 'store', 'show']);
    });
});


/*
|--------------------------------------------------------------------------
| Load Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/projects.php';
require __DIR__ . '/accounting.php';
require __DIR__ . '/requests.php';
require __DIR__ . '/sinrinbo.php';
require __DIR__ . '/attendance.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/shared.php';
require __DIR__ . '/master.php';
