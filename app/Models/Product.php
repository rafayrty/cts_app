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
     * Get the documents for the product.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the document pages for the product.
     */
    public function document_pages()
    {
        return $this->hasManyThrough(Document::class, DocumentPage::class);
    }

    /**
     * Get the category for the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
