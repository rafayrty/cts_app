<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
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

        //Create age groups
        $data = [
            'name' => 'العمر',
        ];
        $product_attribute = ProductAttribute::create($data);
        ProductAttributeOption::create(['product_attribute_id' => $product_attribute->id, 'name' => 'من سن 0 الى 3 سنوات', 'slug' => 'من-سن-0-الى-3-سنوات']);
        ProductAttributeOption::create(['product_attribute_id' => $product_attribute->id, 'name' => 'من سن 6 الى 3 سنوات', 'slug' => 'من-سن-6-الى-3-سنوات']);
        ProductAttributeOption::create(['product_attribute_id' => $product_attribute->id, 'name' => 'من سن 6 سنوات وأكبر', 'slug' => 'من-سن-6-سنوات-وأكبر']);
    }
}
