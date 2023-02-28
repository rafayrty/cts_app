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

    protected $hidden = ['pagesParsed', 'dedicationsParsed'];

    public $appends = ['pagesParsed', 'dedicationsParsed', 'has_sale', 'front_price', 'has_male', 'has_female', 'age_groups', 'is_in_wishlist'];

    public function getpagesParsedAttribute()
    {
        return is_array($this->pages) ? $this->pages : (array) json_decode($this->pages);
    }

    public function getdedicationsParsedAttribute()
    {
        return is_array($this->dedications) ? $this->dedications : (array) json_decode($this->dedications);
    }

    public function getAgeGroupsAttribute()
    {
        $attributes = $this->product_attributes()->where('product_attribute_id', 2)->get();

        return $attributes;
    }

    /**
     * Check if the product is discounted
     */
    public function getHasSaleAttribute()
    {
        if ($this->discount_percentage) {
            return true;
        }

        return false;
    }

    /**
     * The Product Price after Calculations
     */
    public function getFrontPriceAttribute()
    {
        if ($this->discount_percentage) {
            return $this->price - ($this->discount_percentage / 100) * $this->price;
        }

        return $this->price;
    }

    /**
     * The Product that belongs to the many products
     */
    public function covers()
    {
        return $this->belongsToMany(Covers::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function product_attributes()
    {
        return $this->belongsToMany(ProductAttributeOption::class);
    }

    /**
     * Check if it is also for males
     */
    public function getHasMaleAttribute()
    {
        $documents = $this->documents()->get();
        foreach ($documents as $document) {
            return in_array('Male', $document->genderParsed);
        }

        return false;
    }

    /**
     * Check if it is also for males
     */
    public function getMaleDocument()
    {
        $documents = $this->documents()->get();
        $filtered_documents = collect([]);
        foreach ($documents as $document) {
            if (in_array('Male', $document->genderParsed)) {
                $filtered_documents->push($document);
            }
        }

        return $filtered_documents;
    }

    /**
     * Check if it is also for males
     */
    public function getFemaleDocument()
    {
        $documents = $this->documents()->get();
        $filtered_documents = collect([]);
        foreach ($documents as $document) {
            if (in_array('Male', $document->genderParsed)) {
                $filtered_documents->push($document);
            }
        }

        return $filtered_documents;
    }

    /**
     * Check if it is also for males
     */
    public function getHasFemaleAttribute()
    {
        $documents = $this->documents()->get();
        foreach ($documents as $document) {
            return in_array('Female', $document->genderParsed);
        }

        return false;
    }

    public function getIsInWishlistAttribute()
    {
        if (request()->user()) {
            $wishlist = Wishlist::where('product_id', $this->id)->where('user_id', request()->user()->id)->get()->count();
            if ($wishlist > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the documents for the product.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the wishlist item for the product.
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the category for the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
