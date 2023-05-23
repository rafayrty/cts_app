<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use Cachable;

    protected $fillable = ['name', 'slug', 'featured', 'description', 'image'];

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
