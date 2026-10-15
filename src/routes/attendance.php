<?php

declare(strict_types=1);

use App\Http\Controllers\AttendanceV2Controller;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->name('attendance_v2.')->group(function () {
    Route::get('/attendance/{token}', [AttendanceV2Controller::class, 'create'])
        ->whereUuid('token')
        ->name('create');

    Route::middleware('throttle:30,1')->group(function () {
        Route::post('/attendance/{token}/clock-in', [AttendanceV2Controller::class, 'clockIn'])
            ->whereUuid('token')
            ->name('clockIn');
        Route::post('/attendance/{token}/clock-out', [AttendanceV2Controller::class, 'clockOut'])
            ->whereUuid('token')
            ->name('clockOut');
        Route::post('/attendance/{token}/comment', [AttendanceV2Controller::class, 'updateComment'])
            ->whereUuid('token')
            ->name('updateComment');
    });

    Route::middleware(['auth', 'can:view-reports'])->group(function () {
        Route::get('/attendances', [AttendanceV2Controller::class, 'index'])->name('index');
        Route::get('/attendances/export/csv', [AttendanceV2Controller::class, 'exportCsv'])->name('exportCsv');
        Route::get('/dashboard/widget/attendance', [DashboardController::class, 'attendanceWidget'])->name('widget');

        Route::get('/attendance/{attendance}/edit', [AttendanceV2Controller::class, 'edit'])->name('edit');
        Route::put('/attendance/{attendance}', [AttendanceV2Controller::class, 'update'])->name('update');
        Route::get('/attendance/create-manual/{worker}', [AttendanceV2Controller::class, 'createManual'])->name('createManual');
        Route::post('/attendance/store-manual/{worker}', [AttendanceV2Controller::class, 'storeManual'])->name('storeManual');
    });
});
