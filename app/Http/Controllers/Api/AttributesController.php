<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;

class AttributesController extends Controller
{
    public function get_all_attributes()
    {
        return ProductAttribute::with('options')->get();
    }
}
