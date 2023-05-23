<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use Cachable;

    protected $casts = [
        'pages' => 'array',
        'gender' => 'array',
        'dimensions' => 'array',
    ];

    use HasFactory;

    protected $guarded = [];

    public $appends = ['genderParsed'];

    public function getgenderParsedAttribute()
    {
        return is_array($this->gender) ? $this->gender : (array) json_decode($this->gender);
    }

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
