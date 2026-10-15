<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Master\User' => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        /**
         * Gate 定義例
         * usersテーブルの auth_type カラムを利用
         * 0 = 管理者, 1 = 一般利用者, 2 = ゲスト
         */

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
