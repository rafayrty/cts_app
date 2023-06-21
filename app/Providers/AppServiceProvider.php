<?php

namespace App\Providers;

use App\Filament\Resources\OrderResource\Pages\PrintingOrders;
use App\FilamentUserResource as ResourcesFilamentUserResource;
use Chiiya\FilamentAccessControl\Resources\FilamentUserResource;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
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
        Filament::registerNavigationItems(
        [
            NavigationItem::make('Orders Printing')
                ->url('/admin/orders/printing')
                ->icon('heroicon-o-link')
                ->group('Order Management')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.orders.printing'))
                ->sort(1),
            NavigationItem::make('Orders Packaging')
                ->url('/admin/orders/packaging')
                ->icon('heroicon-o-link')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.orders.packaging'))
                ->group('Order Management')
                ->sort(2)
        ]);
        $this->app->bind(FilamentUserResource::class, function () {
            return new ResourcesFilamentUserResource;
        });
    }
}
