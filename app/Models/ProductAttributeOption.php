<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeOption extends Model
{
    use HasFactory;
    use Cachable;

    protected $guarded = [];

    /**
     * Get the Product attribute
     */
    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    /**
     *  Get the Product
     */
    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
