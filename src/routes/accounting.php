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

    // Invoices
    // （旧: 先頭の prefix グループに create-from-estimate が二重定義されていたため削除）
    Route::controller(InvoiceController::class)->prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/create-from-estimate/{estimate}', 'createFromEstimate')->name('create-from-estimate');

        // 送付案内
        Route::get('/transmittal', 'transmittalForm')->name('transmittal');
        Route::post('/transmittal_pdf', 'generateTransmittalPdf')->name('transmittal_pdf'); // GET→POST

        // プレビュー・PDF
        Route::post('/preview', 'preview')->name('preview');
        Route::get('/{invoice}/pdf', 'downloadPdf')->name('pdf');
        Route::patch('/{invoice}/restore', 'restore')->name('restore')->withTrashed();
    });

    // 通常のCRUDルート
    Route::resource('invoices', InvoiceController::class)->except(['show']);

    // Transactions（電子帳簿保存）
    // ※ {transaction} より前に定義すること（"saved" が ID として解釈されるのを防ぐ）
        Route::get('transactions/saved', [TransactionController::class, 'saved'])->name('transactions.saved');
    Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export'); // 追加

    // 更新(edit/update)は存在しない。destroy は「取消」（論理削除）。取消済みの詳細も show で閲覧可能
    Route::resource('transactions', TransactionController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy'])
        ->withTrashed(['show']);

    Route::get('/transaction-files/{file}/view', [TransactionController::class, 'download'])
        ->name('transaction-files.view');

    // purchase-orders
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
