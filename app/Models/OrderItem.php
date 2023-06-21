<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $appends = ['nameParsed'];

    protected $casts = ['product_info' => 'array', 'cover' => 'array', 'inputs' => 'array'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //public function cover()
    //{
        //return $this->hasOne(Covers::class);
    //}
    public function getNameParsedAttribute()
    {

        $product = $this->product;
        $name = $this->inputs['name'];

        return str_replace('{basmti}', $name, $product->demo_name);
    }
}
