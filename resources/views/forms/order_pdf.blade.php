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
$items = $order->items;
$urls = [];
$ids = [];

$fonts = \App\Models\Fonts::all();
$fonts_array = ['GE-Dinar-Medium' => 'GE-Dinar-Medium'];
foreach ($fonts as $font) {
    $fonts_array = array_merge($fonts_array, [$font->font_name => $font->font_name]);
}

@endphp
<div class="filament-forms-card-component p-6 bg-white rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800">
<!-- Order Fonts Updater -->

<h2 class="text-success-500">Font Updated Successfully</h2>
  <div class="flex items-center justify-between">

@if(Session::has('success'))

@endif
  <p>Order Documents Summary</p>
    @php
        $invoice_info = json_decode($order->invoice_info,true);
        $invoice_url = null;
        if($invoice_info){
            if(array_key_exists('doc_url',$invoice_info)){
                $invoice_url = $invoice_info['doc_url'];
            }
        }
    @endphp
<div>
  <a href="{{route('order.internal_invoice',$order->id)}}" id="view-invoice" class="filament-link inline-flex  font-medium hover:underline focus:outline-none focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action"> View Internal Invoice </a>
  <span>|</span>
@if($invoice_url)
  <a href="{{$invoice_url}}" id="view-invoice" class="filament-link inline-flex  font-medium hover:underline focus:outline-none focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action"> View Invoice </a>
  <span>|</span>
@endif
  <a href="{{route('order.download.pdf',$order->id)}}" id="download-all" class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action"> Download All </a>
  </div>
  </div>
  @foreach ($items as $item)

  @php
    $documents = $item->product->documents ?? [];
  @endphp
  <h2 class="text-xl font-semibold mt-4">{{ str_replace("{basmti}",$item->inputs['name'],$item->product->demo_name ?? $item->product_info['demo_name'])}}</h2>
  @foreach ($documents as $document)

@if($item->product_type==1)
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

@endif
  @if($item->product_type==2)
  {{--@if($item->language == 'english' && $document->type == 0)--}}
  @if($item->language == 'english')
  @if($document->type == 0 && $document->language == 'english')
    <a href="{{route('order.preview.pdf',['id'=>$document->id,'order_item_id'=>$item->id])}}" target="_blank" class="filament-button mt-4 filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
    {{ str_replace("{basmti}",$item->inputs['name'],$item->product->demo_name)}} - NotebookCover {{$item->language}}</a>
  <div class="product-info mt-2">
  <ul>
      <li><strong>Number of Packages: </strong>{{$item->quantity}}</li>
      <li><strong>Number of Notebooks: </strong>{{$item->quantity * 4}}</li>
      <li><strong>Language: </strong>{{$item->language}}</li>
  </ul>
  </div>
  @endif
  @else
      @if($document->type == 0 && $document->language !='english')
        <a href="{{route('order.preview.pdf',['id'=>$document->id,'order_item_id'=>$item->id])}}" target="_blank" class="filament-button mt-4 filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
        {{ str_replace("{basmti}",$item->inputs['name'],$item->product->demo_name)}} - NotebookCover {{$item->language}}</a>
  <div class="product-info mt-2">
  <ul>
      <li><strong>Number of Packages: </strong>{{$item->quantity}}</li>
      <li><strong>Number of Notebooks: </strong>{{$item->quantity * 4}}</li>
      <li><strong>Language: </strong>{{$item->language}}</li>
  </ul>
      </div>
      @endif
  @endif

  @endif
  @endforeach


    <div class="mt-4">

    <strong>Update Font For Product</strong>
<form method="post" action="{{route('order.update_font',['id'=>$item->product ? $item->product->id : 0,'order_id'=>$order->id])}}">
@csrf
<select name="font" class="mt-2 filament-forms-input block w-full rounded-lg text-gray-900 shadow-sm outline-none transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
<option selected>Select a Font</option>
@foreach($fonts_array as $value)
    <option value="{{$value}}">{{$value}}</option>
@endforeach

</select>
<button type="submit" class="filament-button mt-4 filament-button-size-md inline-flex items-center justify-end py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">Update</button>
</form>
    </div>

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

