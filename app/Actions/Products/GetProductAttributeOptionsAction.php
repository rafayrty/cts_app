<?php

namespace App\Actions\Products;

use App\Models\ProductAttributeOption;

class GetProductAttributeOptionsAction
{
    public function __invoke($id, $limit = 4)
    {
        $result = ProductAttributeOption::where('product_attribute_id', $id)->skip(0)->take($limit)->get();
        if (! $result) {
            abort(404);
        }

        return $result;
    }
}
