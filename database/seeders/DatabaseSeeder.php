<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        //Create Genders
        $this->call([
            ProductAttributeSeeder::class,
        ]);
        Permission::create(['guard_name' => 'filament', 'name' => 'products.viewAny']);
        Permission::create(['guard_name' => 'filament', 'name' => 'products.view']);
        $role = Role::where('name', 'super-admin')->first();
        $role->givePermissionTo(Permission::all());

    }
}
