<?php

namespace App\Actions\Categories;

use App\Models\Category;

class GetFeaturedCategoriesAction
{
    public function __invoke($limit = 4)
    {
        return Category::where('featured', 1)->skip(0)->take($limit)->get();
    }
}
