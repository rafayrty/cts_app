<?php

namespace App\Providers;

use App\FilamentUserResource as ResourcesFilamentUserResource;
use Chiiya\FilamentAccessControl\Resources\FilamentUserResource;
use Filament\Facades\Filament;
use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;

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
        Filament::serving(function () {
            // Using Vite
            Filament::registerViteTheme('resources/css/filament.css');
        });
        Filament::registerNavigationGroups([
            'Order Management',
            'Product Management',
            'Administration',
        ]);
        $this->app->bind(FilamentUserResource::class, function () {
            return new ResourcesFilamentUserResource;
        });
    }
}
