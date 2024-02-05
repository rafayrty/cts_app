<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    /**
     * Get the addresses for the user.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
