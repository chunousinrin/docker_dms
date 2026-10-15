<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\{
    InvoiceController,
    EstimateController,
    Estimate2Controller,
    TransactionController
};
use App\Http\Controllers\Accounting\PurchaseOrderController;

Route::middleware('auth')->group(function () {

    // Estimates
    Route::prefix('estimates')->name('estimates.')->group(function () {
        Route::post('/preview', [EstimateController::class, 'preview'])->name('preview');
        Route::get('/{estimate}/pdf', [EstimateController::class, 'pdf'])->name('pdf');
    });
    Route::resource('estimates', EstimateController::class);

    // Estimate2
    Route::prefix('estimate2')->name('estimate2.')->group(function () {
        Route::post('/preview', [Estimate2Controller::class, 'preview'])->name('preview');
        Route::get('/pdf/{id}', [Estimate2Controller::class, 'showPdf'])->name('showPdf');
    });
    Route::resource('estimate2', Estimate2Controller::class)->except('show');


    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/create-from-estimate/{estimate}', [InvoiceController::class, 'createFromEstimate'])->name('create-from-estimate');
    });


    // Invoices
    Route::controller(InvoiceController::class)->prefix('invoices')->name('invoices.')->group(function () {
        // 送状（transmittal）関連のルート
        Route::get('/transmittal', 'transmittalForm')->name('transmittal');

        // ★ ここを Route::post から Route::get に変更します
        Route::get('/transmittal_pdf', 'generateTransmittalPdf')->name('transmittal_pdf');

        // プレビュー・ダウンロード系
        Route::post('/preview', 'preview')->name('preview');
        Route::get('/{invoice}/download', 'download')->name('download');
        Route::get('/{invoice}/pdf', 'downloadPdf')->name('pdf');
    });

    // 通常のCRUDルート
    Route::resource('invoices', InvoiceController::class)->except(['show']);
    // Transactions
    Route::resource('transactions', TransactionController::class);

    Route::get('/transaction-files/{file}/view', [TransactionController::class, 'download'])
        ->name('transaction-files.view');
    Route::get('/transactions/{transaction}/show', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');


    //purchase-orders
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
        Route::put('/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('update');
        Route::delete('/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
        // PDF
        Route::post('/pdf-preview', [PurchaseOrderController::class, 'pdfPreview'])->name('pdf-preview');
        Route::get('/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'pdf'])->name('pdf');
    });
});
