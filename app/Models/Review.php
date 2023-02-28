<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['profile_image', 'name'];

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

    public function getNameAttribute()
    {
        return $this->user->first_name.' '.$this->user->last_name;
    }

    public function getProfileImageAttribute()
    {
        return 'https://ui-avatars.com/api/?name='.$this->user->first_name.' '.$this->user->last_name;
    }
}
