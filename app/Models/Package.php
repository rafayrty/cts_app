<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $appends = ['route_name'];

    public function delivery_routes()
    {
        return $this->belongsTo(DeliveryRoutes::class);
    }

    /**
     * @return <missing>|array
     */
    public function getRouteNameAttribute()
    {
        return $this->from.' -> '.$this->to;
    }
}
