<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    label=""
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
<style>
.order-info-list li{
  display:flex;
  justify-content:space-between;
}
.billing-info-list li{
  display:flex;
  align-items:center;
  justify-content:space-between;
}
</style>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }">
@php
$order = \App\Models\Order::where('order_numeric_id',$evaluate(fn ($get) => $get('order_numeric_id')))->get()->first();

\App\Models\Order::find($order->id)->update(['is_viewed'=>true]);
$user_count =  \App\Models\Order::where('user_id',$order->user_id)->count();
$amount_spent =  \App\Models\Order::where('user_id',$order->user_id)->sum('total');
@endphp
<h1 class="mt-4 text-xl font-bold text-gray-400" style="margin-top:-.5rem;">Order ID# {{$order->order_numeric_id}}</h1>
      <ul class="order-info-list mt-2">
          <li><strong>Client Status: </strong>{{$order->client_status}}</li>
          <li class="mt-2"><strong>Payment Status: </strong>{{$order->payment_status}}</li>
      </ul>
    <h1 class="mt-4 text-xl font-bold text-gray-400">Customer Info:</h1>
      <ul class="order-info-list mt-4">
          <li><strong>Full Name:</strong>{{$order->user->full_name ?? ''}}</li>
          <li class="mt-2"><strong>Email:</strong><a href="mailto:{{$order->user->email ?? ''}}" class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action">{{$order->user->email ?? ''}}</a></li>
          <li class="mt-2"><strong>Phone :</strong><a class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action" href="tel:+{{$order->user->country_code ?? ''}}{{$order->user->phone ?? ''}}">
          +{{$order->user->country_code ?? ''}}{{$order->user->phone ?? ''}}</a></li>
          <li class="mt-2"><strong>Order Count:</strong> {{$user_count}}</li>
          <li class="mt-2"><strong>Amount Spent:</strong>₪ {{$amount_spent/100}}</li>
          @if($order->user->referral)
            <li class="mt-2"><strong>From Referral:</strong> {{$order->user->referral}}</li>
         @endif
      </ul>
    <h1 class="mt-4 text-xl font-bold text-gray-400">Billing Info:</h1>
    <ul class="billing-info-list">
      @if($order->coupon)
            <li><strong>Applied Coupon:</strong>{{$order->coupon}}</li>
            @php
            $coupon = \App\Models\Coupon::where('coupon_name',$order->coupon)->get()->first();
            @endphp

            @if($coupon)
                @if($coupon->free_shipping)
                    <li><strong style="color:red;">Coupon Gives Free Shipping</strong></li>
                @endif
            @endif
      @endif
            <li class="mt-2"><strong>Subtotal:</strong>₪ {{$order->sub_total/100}}</li>
            <li  class="mt-2"><strong>Shipping:</strong>₪ {{$order->shipping/100}}</li>
            <li  class="mt-2"><strong>Discount Total:</strong>₪ {{$order->discount_total/100}}</li>
            <li  class="mt-2"><strong>Total:</strong><b class="text-2xl">₪ {{$order->total/100}}</b></li>
          </ul>
    </div>
</x-dynamic-component>
