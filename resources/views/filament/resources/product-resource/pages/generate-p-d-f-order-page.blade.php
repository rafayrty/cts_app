<x-filament::page>
<div class="filament-forms-card-component p-6 bg-white rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800">
  <p>Select a Document to Proceed</p>
  @foreach ($items as $item)

  @php
    $documents = $item->product->documents;
  @endphp

  <h2 class="text-xl font-semibold mt-4">{{ str_replace("{basmti}",$item->inputs['name'],$item->product->demo_name)}}</h2>
  @foreach ($documents as $document)
  @if($document->type == ($item->cover['type'] == 2 ? 0 : 1) || $document->type  == 2)
<a href="{{route('order.preview.pdf',['id'=>$document->id,'order_item_id'=>$item->id])}}" target="_blank" class="filament-button mt-4 filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
{{ str_replace("{basmti}",$item->inputs['name'],$item->product->demo_name)}} - @if($document->type==1)
Hard Cover
@elseif($document->type==2)
Book
@else
Soft Cover
@endif
</a>
@endif
  @endforeach

  @endforeach
</div>
</x-filament::page>
