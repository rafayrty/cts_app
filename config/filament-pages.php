<?php

use App\Filament\FilamentPageTemplates\AboutPage;
use App\Filament\FilamentPageTemplates\ContactPage;
use App\Filament\FilamentPageTemplates\HomePage;
use App\Filament\FilamentPageTemplates\PoliciesPage;
use App\Filament\Resources\CustomPageResource;
use App\Models\FilamentPage as ModelsFilamentPage;
use Beier\FilamentPages\Filament\FilamentPageTemplates\DefaultTemplate;
use Beier\FilamentPages\Filament\Resources\FilamentPageResource;
use Beier\FilamentPages\Models\FilamentPage;
use Beier\FilamentPages\Renderer\SimplePageRenderer;

return [
    'filament' => [
        /*
        |--------------------------------------------------------------------------
        | Filament: Custom Filament Resource
        |--------------------------------------------------------------------------
        |
        | Use your own extension of the FilamentPageResource
        | below to fully customize every aspect of it.
        |
        */
        'resource' => CustomPageResource::class,
        /*
        |--------------------------------------------------------------------------
        | Filament: Custom Filament Model
        |--------------------------------------------------------------------------
        |
        | Use your own extension of the FilamentPage Model
        | below to fully customize every aspect of it.
        |
        */
        'model' => ModelsFilamentPage::class,
        /*
        |--------------------------------------------------------------------------
        | Filament: Title Attribute
        |--------------------------------------------------------------------------
        |
        | Point to another field or Attribute to change the
        | computed record title provided in filament.
        |
        */
        'recordTitleAttribute' => 'title',
        /*
        |--------------------------------------------------------------------------
        | Filament: Label
        |--------------------------------------------------------------------------
        |
        | If you don't need to support multiple languages you can
        | globally change the model label below. If you do,
        | you should rather change the translation files.
        |
        */
        'modelLabel' => 'Page',
        /*
        |--------------------------------------------------------------------------
        | Filament: Plural Label
        |--------------------------------------------------------------------------
        |
        | If you don't need to support multiple languages you can
        | globally change the plural label below. If you do,
        | you should rather change the translation files.
        |
        */
        'pluralLabel' => 'Pages',
        'navigation' => [
            /*
            |--------------------------------------------------------------------------
            | Filament: Navigation Icon
            |--------------------------------------------------------------------------
            |
            | If you don't need to support multiple languages you can
            | globally change the navigation icon below. If you do,
            | you should rather change the translation files.
            |
            */
            'icon' => 'heroicon-o-document',
            /*
            |--------------------------------------------------------------------------
            | Filament: Navigation Group
            |--------------------------------------------------------------------------
            |
            | If you don't need to support multiple languages you can
            | globally change the navigation group below. If you do,
            | you should rather change the translation files.
            |
            */
            'group' => 'Administration',
            /*
            |--------------------------------------------------------------------------
            | Filament: Navigation Group
            |--------------------------------------------------------------------------
            |
            | If you don't need to support multiple languages you can
            | globally change the navigation sort below. If you do,
            | you should rather change the translation files.
            |
            */
            'sort' => 1,
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | Add your own Templates implementing FilamentPageTemplate::class
    | below. They will appear in the Template selection,
    | and persisted to the data column.
    |
    */
    'templates' => [
        DefaultTemplate::class,
        HomePage::class,
        PoliciesPage::class,
        AboutPage::class,
        ContactPage::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Renderer
    |--------------------------------------------------------------------------
    |
    | If you want to use the Rendering functionality, you can create your
    | own Renderer here. Take the available Renderers for reference.
    | See FilamentPageController for recommended usage.
    |
    | Available Renderers:
    | - SimplePageRenderer:
    |   Renders everything to the defined layout below.
    | - AtomicDesignPageRenderer:
    |   More opinionated Renderer to be used with Atomic Design.
    |
    | To use the renderer, Add a Route for the exemplary FilamentPageController:
    |
    |  Route::get('/{filamentPage}', [FilamentPageController::class, 'show']);
    |
    | To route the homepage, you could add a data.is_homepage
    | field and query it in a controller.
    |
    */
    'renderer' => SimplePageRenderer::class,

    /*
    |--------------------------------------------------------------------------
    | Simple Page Renderer: Default Layout
    |--------------------------------------------------------------------------
    |
    | Only applicable to the SimplePageRenderer.
    |
    */
    'default_layout' => 'layouts.app',
];
