<div class="flex items-center justify-between">
  <div class="flex flex-col items-center justify-center">
      <h1 class="font-bold text-lg">New Orders</h1>
      <p class="text-lg">{{$order_new_order}}</p>
  </div>

  <div class="flex flex-col items-center justify-center">
      <h1 class="font-bold text-lg">Orders Printing</h1>
      <p class="text-lg">{{$order_printing_status}}</p>
  </div>

  <div class="flex flex-col items-center justify-center">
      <h1 class="font-bold text-lg">Orders In Delivery</h1>
      <p class="text-lg">{{$order_in_delivery}}</p>
  </div>

</div>
<style>
#orderStatusChart .apexcharts-text.apexcharts-datalabel-value{
    fill:#8da12b !important;
}
</style>
