<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the user for the address
     */
    public function user()
    {
        return $this->belongsTo(Address::class);
    }
}
