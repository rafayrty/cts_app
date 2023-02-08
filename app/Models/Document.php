<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $casts = [
        'pages' => 'array',
    ];

    use HasFactory;

    protected $guarded = [];

    /**
     * Get the documents for the product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the documents for the product.
     */
    public function document_pages()
    {
        return $this->hasMany(DocumentPage::class);
    }
}
