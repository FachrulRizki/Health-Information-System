<?php

namespace App\Providers;

use App\Http\ViewComposers\NavigationComposer;
use Illuminate\Support\Facades\View;
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
        // Share $userPermissions with all views via NavigationComposer
        View::composer('*', NavigationComposer::class);
    }
}
