<div>
    @php
        $record = $getRecord();
    @endphp
                            <a href='{{route('order.download.pdf', $record->id)}}' style='display:flex;' class='
                        filament-link relative inline-flex items-center justify-center font-medium outline-none hover:underline focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action'>

@if($record->queue_status === 'processing')
<svg class='animate-spin -ml-1 mr-3 h-4 w-4 text-white' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'>
      <circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle>
      <path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path>
    </svg>
@endif
                        <span>

                        Download All</span></a>
</div>

