<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Forest\ForestSearchController;

Route::prefix('sinrinbo')->name('sinrinbo.')->group(function () {

    Route::view('/login', 'work.sinrinbo.login')->name('login');

    Route::post('/login', function (Request $request) {

        if ($request->password === config('sinrinbo.password')) {
            $request->session()->put('is_authenticated', true);
            return redirect()->route('sinrinbo.search');
        }

        return back()->with('error', 'パスワードが違います');
    });

    Route::get('/logout', function (Request $request) {
        $request->session()->forget('is_authenticated');
        return redirect()->route('dashboard')->with('success', 'ログアウトしました');
    })->name('logout');

    Route::middleware(App\Http\Middleware\SimpleAuthMiddleware::class)->group(function () {
        Route::get('/', [ForestSearchController::class, 'index'])->name('search');
        Route::get('/options', [ForestSearchController::class, 'getOptions'])->name('options');
        Route::get('/export', [ForestSearchController::class, 'export'])->name('export');
    });
});
