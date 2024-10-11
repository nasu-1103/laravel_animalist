<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // ユーザーが管理者かどうかを判定すための条件を指定
        Gate::define('admin', function (User $user) {
            // ユーザーの'admin_flg'が1であれば、管理者として認識する
            return $user->admin_flg === 1;
        });
    }
}
