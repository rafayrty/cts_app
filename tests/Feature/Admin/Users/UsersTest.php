<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use function Pest\Laravel\artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('check if a admin user is created and show in table', function () {
    //$role = Role::create(['name' => 'super_admin']);
    //Artisan::call("shield:install --no-interaction");
    $user = User::factory()->create(['email_verified_at' => null]);

    //$this->artisan('shield:install --fresh')
    //->expectsConfirmation('Do you wish to continue?', 'yes')
    //->expectsConfirmation('Would you like to show some love by starring the repo?', 'no')
    //->assertExitCode(0);
    //Artisan::call("shield:super-admin --user=".$user->id);
    //Artisan::call("permission:cache-reset");
    //$user = User::findOrFail($user->id);
});
