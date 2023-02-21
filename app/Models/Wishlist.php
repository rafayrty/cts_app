<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the Product for the wishlist.
     */
    public function product()
    {
        return $this->hasOne(Product::class);
    }

    /**
     * Get the User for the wishlist.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
