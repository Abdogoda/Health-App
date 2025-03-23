<?php

namespace App\Providers;

use App\Models\Progress;
use App\Models\UserProfile;
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
        UserProfile::observe(\App\Observers\UserProfileObserver::class);
        Progress::observe(\App\Observers\ProgressObserver::class);
    }
}
