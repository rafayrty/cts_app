<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Covers extends Model
{
    protected $guarded = [];

    use HasFactory;
    use Cachable;

    /**
     * The products that have many covers
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
