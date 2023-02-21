<x-filament::page>
<div class="filament-forms-card-component p-6 bg-white rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800">
  <p>Select a Document to Proceed</p>
  @php
    $documents = $product->documents;
  @endphp
  @foreach ($documents as $document)
<a href="{{route('preview.pdf',$document->id)}}" target="_blank" class="filament-button mt-4 filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
  {{$document->name}}
</a>
  @endforeach
</div>
</x-filament::page>
