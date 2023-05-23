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
        $data = [
            'name' => 'الجنس',
        ];
        $product_attribute = ProductAttribute::create($data);
        ProductAttributeOption::create(['product_attribute_id' => $product_attribute->id, 'name' => 'ذكر', 'slug' => 'Male']);
        ProductAttributeOption::create(['product_attribute_id' => $product_attribute->id, 'name' => 'انثى', 'slug' => 'Female']);

        $data = [
            'name' => 'العمر',
        ];
        $product_attribute = ProductAttribute::create($data);

        ProductAttributeOption::create(['product_attribute_id' => $product_attribute->id, 'name' => 'من سن 0 الى 3 سنوات', 'slug' => 'من-سن-0-الى-3-سنوات']);
        ProductAttributeOption::create(['product_attribute_id' => $product_attribute->id, 'name' => 'من سن 6 الى 3 سنوات', 'slug' => 'من-سن-6-الى-3-سنوات']);
        ProductAttributeOption::create(['product_attribute_id' => $product_attribute->id, 'name' => 'من سن 6 سنوات وأكبر', 'slug' => 'من-سن-6-سنوات-وأكبر']);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        Permission::create(['guard_name' => 'filament','name' => 'products.viewAny']);
        Permission::create(['guard_name' => 'filament','name' => 'products.view']);
        $role = Role::where('name', 'super-admin')->first();
        $role->givePermissionTo(Permission::all());

    }
}
