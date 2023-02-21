<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the product for the review.
     */
    public function product()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the user for the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
