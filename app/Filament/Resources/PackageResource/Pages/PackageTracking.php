<?php

namespace App\Filament\Resources\PackageResource\Pages;

use App\Filament\Resources\PackageResource;
use App\Models\Package;
use Filament\Resources\Pages\Page;

class PackageTracking extends Page
{
    protected static string $resource = PackageResource::class;

    protected static string $view = 'filament.resources.package-resource.pages.package-tracking';

    //public function __construct()
    //{
        //abort_unless(auth()->user()->can('package.view'), 403);
    //}

}
