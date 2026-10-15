<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // 追加
use App\Models\Master\User; // 追加
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();

        Gate::define('manage-users', function ($user) {
            return $user->auth_type === 0; // 管理者のみ
        });

        Gate::define('view-reports', function ($user) {
            return in_array($user->auth_type, [0, 1]); // 管理者と一般利用者
        });

        Gate::define('guest-access', function ($user) {
            return $user->auth_type === 2; // ゲストのみ
        });
    }
}
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/login';
}
