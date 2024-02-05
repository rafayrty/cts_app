<!DOCTYPE html>
<html>
<head>
    <title>Basmt Invoice</title>
</head>
<style type="text/css">
    body{
     font-family: DejaVu Sans, sans-serif;
    }
    .m-0{
        margin: 0px;
    }
    .p-0{
        padding: 0px;
    }
    .pt-5{
        padding-top:5px;
    }
    .mt-10{
        margin-top:10px;
    }
    .text-center{
        text-align:center !important;
    }
    .w-100{
        width: 100%;
    }
    .w-50{
        width:50%;
    }
    .w-85{
        width:85%;
    }
    .w-15{
        width:15%;
    }
    .logo img{
        width:200px;
        height:60px;
    }
    .gray-color{
        color:#5D5D5D;
    }
    .text-bold{
        font-weight: bold;
    }
    .border{
        border:1px solid black;
    }
    .item-heading{
        border: 1px solid #d2d2d2;
        border-collapse:collapse;
        background: #F4F4F4;
        font-size:15px;
        padding:7px 8px;
        text-align: center;
        font-weight: bold;
    }
    table tr,th,td{
    }
    table tr th{
        background: #F4F4F4;
        font-size:15px;
    }
    table tr td{
        font-size:13px;
    }
    table{
        border-collapse:collapse;
    }
    .box-text p{
        line-height:10px;
    }
    .float-right{
        float:right;
    }
    .float-left{
        float:left;
    }
    .total-part{
        font-size:16px;
        line-height:12px;
    }
    .total-right p{
        padding-right:20px;
    }
</style>
<body>
<div class="head-title">
    <h1 class="text-center m-0 p-0">Basmti Invoice</h1>
</div>
<div class="add-detail mt-10">
    <div class="w-50 float-left mt-10" style="text-align: center;">
        <img src="{{DNS1D::getBarcodePNGPath($order->order_numeric_id, 'C128', 1, 55, [0, 0, 0], true)}}" alt="Logo" style="display:inline-block;margin-top:1rem;">
    </div>
    <div class="w-50 float-left logo mt-10">
        <p class="m-0 pt-5 text-bold w-100">Customer Name - <span class="gray-color">{{$order->user ? $order->user->full_name : ""}}</span></p>
        <p class="m-0 pt-5 text-bold w-100">Customer Phone - <span class="gray-color">{{$order->user ? $order->user->phone_number : ""}}</span></p>
        <p class="m-0 pt-5 text-bold w-100">Order Id - <span class="gray-color">{{$order->order_numeric_id}}</span></p>
        <p class="m-0 pt-5 text-bold w-100">Order Date - <span class="gray-color">{{$order->created_at}}</span></p>
    </div>
    <div style="clear: both;"></div>
</div>

<div class="table-section bill-tbl w-100 mt-10">
    <div class="table w-100 mt-10" style="border:1px solid #d2d2d2;">
    @foreach($order->items as $item)
@php
                    $barcodes = [];
                    foreach ($order->barcodes as $barcode) {
                        $parts = explode('-', $barcode['barcode_number']);
                        $last_num = end($parts);
                        if ($last_num == $item->id) {
                            $barcodes[] = $barcode['barcode_number'];
                        }
                    }
@endphp
                <div class="w-100 item-heading">Order Item #{{$item->id}} </div>
            <div class="w-100">
                    <div style="padding:0.5rem;">
                        <div class="float-right box-text" style="width:37%">
                                <p class="m-0 pt-5 text-bold w-100">Product Name - <span class="gray-color">{{$item->nameParsed}}</span></p>
                                @if($item->product_type==2)
                                    <p class="m-0 pt-5 text-bold w-100">Number of Packages - <span class="gray-color">{{$item->quantity}}</span></p>
                                    <p class="m-0 pt-5 text-bold w-100">Number of Notebooks - <span class="gray-color">{{$item->quantity * 4}}</span></p>
                                    <p class="m-0 pt-5 text-bold w-100">Language - <span class="gray-color">{{$item->language}}</span></p>
                                @endif
                        </div>
                        <div class="float-left box-text" style="width:37%">
                            <p class="m-0 pt-5 text-bold w-100">Price - <span class="gray-color">ILS {{$item->total/100}}</span></p>
                            {{--<p class="m-0 pt-5 text-bold w-100">Discount - <span class="gray-color">ILS {{$item->discount_total/100}}</span></p>--}}
                            @if($item->product_type==1)
                                <p class="m-0 pt-5 text-bold w-100">Cover Price - <span class="gray-color">ILS {{$item->cover['price']/100}}</span></p>
                            @endif
                            <p class="m-0 pt-5 text-bold w-100">Quantity - <span class="gray-color">{{$item->quantity}}</span></p>
                        </div>

                        <div style="clear: both;"></div>
                    </div>

                    <div class="w-100" style="padding:0.5rem;">
                        @foreach($barcodes as $barcode)
                        <div class="float-left box-text w-50">
                            <img src="{{DNS1D::getBarcodePNGPath($barcode, 'C128', 1, 55, [0, 0, 0], true)}}" alt="Logo" style="display:inline-block;margin-top:1rem;">
                        </div>
                        @endforeach
                        <div style="clear: both;"></div>
                    </div>

            </div>
    @endforeach
    </div>
</div>
<div class="table-section bill-tbl mt-10 float-right" style="width:35%;">
                <div class="total-part">
                    <div class="total-left float-left" style="width:60%;">
                        <p>Sub Total</p>
                        <p>Shipping</p>
                        <p>Discount Total</p>
                        <p>Total Payable</p>
                    </div>
                    <div class="total-right float-right text-bold" style="width:38%">
                        <p>ILS{{$order->sub_total/100}}</p>
                        <p>ILS{{$order->shipping/100}}</p>
                        <p>ILS{{$order->discount_total/100}}</p>
                        <p>ILS{{$order->total/100}}</p>
                    </div>
                    <div style="clear: both;"></div>
                </div>
</div>

                    <div style="clear: both;"></div>
</html>
