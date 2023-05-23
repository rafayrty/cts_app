<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Settings\GeneralSettings;

class OrderController extends Controller
{
    public function index()
    {
        return Order::where('user_id', request()->user()->id)->with('items')->get();
    }

    public function get_shipping_fee()
    {
        return ['shipping' => app(GeneralSettings::class)->shipping_fee];
    }

    public function show($id)
    {
        return Order::where('user_id', request()->user()->id)->where('id', $id)->with('items')->get()->first();
    }
}
