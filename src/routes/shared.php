<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Master\BankMasterController;
use App\Http\Controllers\Master\ClassificationMasterController;
use App\Http\Controllers\Master\DailyReportMasterController;
use App\Http\Controllers\Master\RingiTypeMasterController;
use App\Http\Controllers\Master\TaxMasterController;
use App\Http\Controllers\Master\DistrictController;
use App\Http\Controllers\Master\SectionController;

use App\Http\Controllers\Shared\DailyReportController;
use App\Http\Controllers\Shared\ProfileController;
use App\Http\Controllers\Shared\TimeRecordController;
use App\Http\Controllers\Shared\TimeRecordManageController;
use App\Http\Controllers\Shared\CapitalTransactionController;
use App\Http\Controllers\Shared\DepositReceiptController;
use App\Http\Controllers\Shared\ForestAreaTransactionController;
use App\Http\Controllers\Shared\NewsletterShipmentController;
use App\Http\Controllers\Shared\NewsletterIssueController;


/*
|--------------------------------------------------------------------------
| 認証あり
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // プロフィール
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });

    // 日報
    Route::resource('daily-reports', DailyReportController::class);

    // 勤怠管理
    Route::prefix('time-records/manage')->name('time_records.manage.')->group(function () {
        Route::get('/', [TimeRecordManageController::class, 'index'])->name('index');
        Route::get('/export', [TimeRecordManageController::class, 'export'])->name('export');
        Route::get('/pdf', [TimeRecordManageController::class, 'pdf'])->name('pdf');
    });

    // マスタ
    Route::middleware('can:manage-users')
        ->prefix('master')
        ->name('master.')
        ->group(function () {

            Route::resource('banks', BankMasterController::class)->except('show');
            Route::resource('classifications', ClassificationMasterController::class)->except('show');
            Route::resource('ringi', RingiTypeMasterController::class)->except('show');
            Route::resource('daily-reports', DailyReportMasterController::class)->except('show');
            Route::resource('taxes', TaxMasterController::class)->except('show');

            // 地区・班
            Route::resource('districts', DistrictController::class)
                ->only(['index', 'store', 'update', 'destroy']);

            Route::resource('sections', SectionController::class)
                ->only(['index', 'store', 'update', 'destroy']);

            // 地区 → 班
            Route::get(
                'api/districts/{district}/sections',
                [SectionController::class, 'byDistrict']
            )->name('districts.sections.json');
        });

    // 組合員関連
    Route::prefix('members/{member}')->group(function () {

        // 出資金
        Route::post('capital-transactions', [CapitalTransactionController::class, 'store'])
            ->name('members.capital-transactions.store');
        Route::delete('capital-transactions/{transactionId}', [CapitalTransactionController::class, 'destroy'])
            ->name('members.capital-transactions.destroy');

        // 森林面積
        Route::post('forest-area-transactions', [ForestAreaTransactionController::class, 'store'])
            ->name('members.forest-area-transactions.store');
        Route::delete('forest-area-transactions/{transactionId}', [ForestAreaTransactionController::class, 'destroy'])
            ->name('members.forest-area-transactions.destroy');

        // 預金・受取
        Route::post('deposit-receipts', [DepositReceiptController::class, 'store'])
            ->name('members.deposit-receipts.store');
        Route::delete('deposit-receipts/{receiptId}', [DepositReceiptController::class, 'destroy'])
            ->name('members.deposit-receipts.destroy');

        // 広報誌発送
        Route::post('newsletter-shipments', [NewsletterShipmentController::class, 'store'])
            ->name('members.newsletter-shipments.store');
        Route::delete('newsletter-shipments/{shipmentId}', [NewsletterShipmentController::class, 'destroy'])
            ->name('members.newsletter-shipments.destroy');
    });

    // 広報誌
    Route::resource('newsletter-issues', NewsletterIssueController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // 広報誌一括発送
    Route::post(
        'newsletter-issues/{newsletterIssue}/ship-to-all',
        [NewsletterIssueController::class, 'shipToAll']
    )->name('newsletter-issues.ship-to-all');
});


/*
|--------------------------------------------------------------------------
| 認証なし：打刻
|--------------------------------------------------------------------------
*/
Route::prefix('time-records')->name('time_records.')->group(function () {

    // ユーザー一覧
    Route::get('/', [TimeRecordController::class, 'userIndex'])->name('users');

    // 打刻画面
    Route::get('/{user}', [TimeRecordController::class, 'show'])->name('show');

    // 打刻
    Route::post('/{user}/punch', [TimeRecordController::class, 'punch'])->name('punch');

    // メモ
    Route::post('/{user}/note', [TimeRecordController::class, 'saveNote'])->name('note');
});
