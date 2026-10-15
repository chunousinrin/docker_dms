<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\MemberController;
use App\Http\Controllers\StampItemController;


//Route::middleware(['auth', 'admin'])->prefix('master')->name('master.')->group(function () {
Route::middleware(['auth', 'can:manage-users'])->group(function () {
    Route::get('/company', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company', [CompanyController::class, 'update'])->name('company.update');
    Route::resource('users', UserController::class)->except('show');

    // 組合員管理 ============================================================
    //Route::resource('members', MemberController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('members', MemberController::class);
});

// 金券 品目マスタ（管理者のみ） ===============================================
// URL: /master/stamp_items   ルート名: master.stamp_items.*
Route::middleware(['auth', 'can:manage-users'])->prefix('master')->name('master.')->group(function () {
    Route::resource('stamp_items', StampItemController::class)
        ->except('show')
        ->parameters(['stamp_items' => 'item']);
});
