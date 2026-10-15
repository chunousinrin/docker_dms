<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Request\RingiRequestController;

Route::middleware('auth')->group(function () {

    Route::prefix('requests')->name('requests.')->group(function () {

        Route::get('/', [RingiRequestController::class, 'index'])->name('index');
        Route::get('/create', [RingiRequestController::class, 'create'])->name('create');
        Route::post('/', [RingiRequestController::class, 'store'])->name('store');

        Route::get('/{id}', [RingiRequestController::class, 'show'])->name('show');

        Route::get('/{id}/edit', [RingiRequestController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RingiRequestController::class, 'update'])->name('update');
        Route::delete('/{id}', [RingiRequestController::class, 'destroy'])->name('destroy');

        Route::get('/{id}/pdf', [RingiRequestController::class, 'pdf'])->name('pdf');
        Route::post('/pdf-preview', [RingiRequestController::class, 'pdfPreview'])->name('pdf.preview');

        Route::post('/{id}/approve', [RingiRequestController::class, 'approve'])->name('approve');
        Route::post('/{id}/return', [RingiRequestController::class, 'returnRequest'])->name('return');
        Route::post('/{id}/withdraw', [RingiRequestController::class, 'withdraw'])->name('withdraw');
    });
});
