<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class Product extends Model
{
    use Cachable;

    protected $casts = [
        'images' => 'array',
        'pages' => 'array',
        'barcodes' => 'array',
        'dedications' => 'array',
        'languages'=>'array'
    ];

    use HasSEO;
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            clear_cgi_cache();
        });

        static::updated(function ($model) {
            clear_cgi_cache();
        });

        static::deleted(function ($model) {
            clear_cgi_cache();
        });
    }

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
            return ceil($this->price - ($this->discount_percentage / 100) * $this->price);
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
        //$documents = $this->documents()->get();
        //foreach ($documents as $document) {
            //return in_array('Male', $document->genderParsed);
        //}

        return $this->product_attributes()->where('product_attribute_option_id', '1')->count() > 0 ? true : false;
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
            if (in_array('Female', $document->genderParsed)) {
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
        //$documents = $this->documents()->get();
        //foreach ($documents as $document) {
            //return in_array('Female', $document->genderParsed);
        //}
        return $this->product_attributes()->where('product_attribute_option_id', '2')->count() > 0 ? true : false;
        //return false;
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

    public function tags()
    {
        return $this->belongsToMany(Tags::class);
    }

    /**
     * Get the categories for the product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

/**
 * Scope a query to only exclude specific Columns.
 *
 * @author Manojkiran.A <manojkiran10031998@gmail.com>
 *
 * @param  \Illuminate\Database\Eloquent\Builder  $query
 * @return \Illuminate\Database\Eloquent\Builder
 */
public function scopeExclude($query, ...$columns)
{
    if ($columns !== []) {
        if (count($columns) !== count($columns, COUNT_RECURSIVE)) {
            $columns = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($columns)));
        }

        return $query->select(array_diff($this->getTableColumns(), $columns));
    }

    return $query;
}

/**
 * Shows All the columns of the Corresponding Table of Model
 *
 * @author Manojkiran.A <manojkiran10031998@gmail.com>
 * If You need to get all the Columns of the Model Table.
 * Useful while including the columns in search
 *
 * @return array
 **/
public function getTableColumns()
{
    return \Illuminate\Support\Facades\Cache::rememberForever('MigrMod:'.filemtime(database_path('migrations')).':'.$this->getTable(), function () {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    });
}
}
