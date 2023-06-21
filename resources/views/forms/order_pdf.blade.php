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
<div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }" style="margin:1rem 0">
@php
$order = \App\Models\Order::where('order_numeric_id',$evaluate(fn ($get) => $get('order_numeric_id')))->get()->first();
// Update to set it as viewed
\App\Models\Order::find($order->id)->update(['is_viewed'=>true]);
//$order->documents();
$items = $order->items;
$urls = [];
$ids = [];
@endphp
<div class="filament-forms-card-component p-6 bg-white rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800">
  <div class="flex items-center justify-between">
  <p>Order Documents Summary</p>
  <a href="{{route('order.download.pdf',$order->id)}}" id="download-all" class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action"> Download All </a>
  </div>
  @foreach ($items as $item)

  @php
    $documents = $item->product->documents ?? [];
  @endphp
  <h2 class="text-xl font-semibold mt-4">{{ str_replace("{basmti}",$item->inputs['name'],$item->product->demo_name)}}</h2>
  @foreach ($documents as $document)


  @if($document->type == ($item->cover['type'] == 2 ? 0 : 1) || $document->type  == 2)
    @php
      $ids[] = $document->id;
      //$urls[] = route('order.download.pdf',['ids'=>$document->id,'order_item_id'=>$item->id]);
    @endphp
<a href="{{route('order.preview.pdf',['id'=>$document->id,'order_item_id'=>$item->id])}}" target="_blank" class="filament-button mt-4 filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
{{ str_replace("{basmti}",$item->inputs['name'],$item->product->demo_name)}} -
@if($document->type==1)
Hard Cover
@elseif($document->type==2)
Book
@else
Soft Cover
@endif
  {{--{{$document->name}}--}}
</a>
@endif
  @endforeach
  @endforeach
</div>
</x-dynamic-component>
@php
@endphp
<script>
/*e.preventDefault()
document.querySelector('#download-all').addEventListener('click',(e)=>{
var urls = JSON.parse(@js(json_encode($urls)));
for (var i = 0; i < urls.length; i++) {
    window.open(urls[i], "_blank");
}
})*/
</script>
