<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Models\Address;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Address::where('user_id', request()->user()->id)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AddressRequest $request)
    {
        $address = Address::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_id' => $request->user()->id,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'backup_phone' => $request->backup_phone,
            'backup_country_code' => $request->backup_country_code,
            'street_name' => $request->street_name,
            'city' => $request->city,
            'street_number' => $request->street_number,
            'home_no' => $request->home_no,
            'message' => $request->message,
        ]);

        return $address;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Only fetch if belongs to the user
        $address = Address::findOrFail($id);

        if ($address->user_id == request()->user()->id) {
            return $address;
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AddressRequest $request, $id)
    {
        $address = Address::findOrFail($id);

        $address->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_id' => $request->user()->id,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'backup_phone' => $request->backup_phone,
            'backup_country_code' => $request->backup_country_code,
            'street_name' => $request->street_name,
            'city' => $request->city,
            'street_number' => $request->street_number,
            'home_no' => $request->home_no,
            'message' => $request->message,
        ]);

        return $address;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $address = Address::findOrFail($id);

        if ($address->user_id == request()->user()->id) {
            return $address->delete();
        }
        abort(404);
    }
}
