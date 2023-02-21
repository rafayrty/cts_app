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

        $id = ProductAttribute::create($data);
        ProductAttributeOption::create(['product_attribute_id' => $id, 'name' => 'ذكر', 'slug' => 'ذكر']);
    }
}
