<?php

namespace App\Actions\Categories;

use App\Models\Category;

class GetAllCategories
{
    public function __invoke()
    {
        return Category::all();
    }
}
