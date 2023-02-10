<?php

namespace App\Providers;

use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\PaymentResource;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;
use io3x1\FilamentUser\Resources\UserResource;
use Spatie\LaravelSettings\Settings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
