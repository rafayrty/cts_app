@php
$order = \App\Models\Order::where('order_numeric_id',$evaluate(fn ($get) => $get('order_numeric_id')))->get()->first();
dd($order);
@endphp

