<?php

namespace App\Http\Controllers\Api;

use App\Actions\Categories\GetAllCategories;
use App\Actions\Categories\GetFeaturedCategoriesAction;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    public function __construct(
        GetFeaturedCategoriesAction $getFeaturedCategoriesAction,
        GetAllCategories $getAllCategories
    ) {
        $this->getFeaturedCategoriesAction = $getFeaturedCategoriesAction;
        $this->getAllCategories = $getAllCategories;
    }

    public function get_featured_categories()
    {
        return ($this->getFeaturedCategoriesAction)();
    }

    public function get_all_categories()
    {
        return ($this->getAllCategories)();
    }
}
