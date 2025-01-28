<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
        Gate::define('canSendMessage', function (User $user, User $toUser) : bool {
            return $user->hasFriend($toUser->id) && $toUser->hasFriend($user->id);
        });
        Schema::defaultStringLength(191);
    }
}
