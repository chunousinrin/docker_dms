<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Project\{
    ProjectController,
    ProjectCostController
};
use App\Http\Controllers\Machinery\{
    MachineryMasterController,
    MachineryMaintenanceController,
    MachineryRefuelController,
    MachineryUsageController,
};
use App\Http\Controllers\ForestPermit\ForestPermitApplicationController;

Route::middleware('auth')->group(function () {
    /* Projects */
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/order-no-candidates', [ProjectController::class, 'orderNoCandidates'])->name('orderNoCandidates');
        Route::post('/pdf-preview', [ProjectController::class, 'pdfPreview'])->name('pdfPreview');
        Route::patch('/{project}/metrics', [ProjectController::class, 'updateMetrics'])->name('updateMetrics');
        Route::get('/{project}/pdf', [ProjectController::class, 'pdf'])->name('pdf');
        Route::resource('/', ProjectController::class)->parameters(['' => 'project']);
    });

    Route::get('/worker-groups/active', [ProjectController::class, 'getActiveWorkerGroups'])->name('workerGroups.active');

    /* Project Costs */
    Route::resource('project-costs', ProjectCostController::class)->only(['store', 'update', 'destroy']);

    // 重機マスター
    Route::resource('machinery-masters', MachineryMasterController::class);

    // 整備履歴
    Route::resource('machinery-masters.maintenances', MachineryMaintenanceController::class)->shallow()->only(['create', 'store', 'destroy']);

    // 給油履歴
    Route::resource('machinery-masters.refuels', MachineryRefuelController::class)->shallow()->only(['create', 'store', 'destroy']);

    // 稼働履歴
    Route::resource('machinery-masters.usages', MachineryUsageController::class)->shallow()->only(['create', 'store', 'destroy']);

    /* Forest Permit */
    Route::resource('forest-permit', ForestPermitApplicationController::class)->except(['show']);
    Route::get('forest-permit/export/csv', [ForestPermitApplicationController::class, 'exportCsv'])->name('forest-permit.export.csv');
});
