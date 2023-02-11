<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'images' => 'array',
        'pages' => 'array',
        'barcodes' => 'array',
        'dedications' => 'array',
    ];

    use HasFactory;

    protected $guarded = [];

    /**
     * The roles that belong to the user.
     */
    public function product_attributes()
    {
        return $this->belongsToMany(ProductAttributeOption::class);
    }

    /**
     * Get the documents for the product.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the category for the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
