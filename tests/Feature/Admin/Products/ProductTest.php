<?php

use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Illuminate\Support\Facades\Artisan;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('Check if Product page is Working', function () {
    Artisan::call('filament-access-control:install');
    $user = FilamentUser::factory()->create();
    /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
    actingAs($user, 'filament');
    get('/admin/products')
    ->assertStatus(200);
});
