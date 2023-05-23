<?php

use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('Check if Product page is Working', function () {
    Artisan::call('filament-access-control:install');
    $this->seed(DatabaseSeeder::class);

    $user = FilamentUser::factory()->create();
    $role = Role::where('name', 'super-admin')->first();
    //dd(Permission::all());
    $user->assignRole($role);
    /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
    actingAs($user, 'filament');
    get('/admin/products')
    ->assertStatus(200);
});
