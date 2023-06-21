<x-filament::widget>
        {{-- Widget content --}}

    @php
if (isset($_GET['tableFilters']['print_house_status']['value'])) {
    $printStatus = $_GET['tableFilters']['print_house_status']['value'] ?? '';
    // Use the $printStatus variable as needed
}

    //Print House
    $statuses = array(
        'new order' => 'rgba(0, 0, 255, 0.3)',          // Blue
        'waiting for approval' => 'rgba(255, 255, 0, 0.3)',  // Yellow
        'approved' => 'rgba(0, 128, 0, 0.3)',           // Green
        'working on it' => 'rgba(255, 165, 0, 0.3)',   // Orange
        'starting printing' => 'rgba(128, 0, 128, 0.3)',   // Purple
        'packaging' => 'rgba(165, 42, 42, 0.3)',        // Brown
        'ready for delivery' => 'rgba(0, 128, 128, 0.3)',  // Teal
        'done' => 'rgba(50, 205, 50, 0.3)',            // Lime Green
        'finishing' => 'rgba(255, 192, 203, 0.3)',      // Pink
        'stuck' => 'rgba(255, 0, 0, 0.3)'               // Red
    );
    $options = [
         'new order' => 'New Order',
         'waiting for approval' => 'Waiting For Approval',
         'approved' => 'Approved',
         'working on it' => 'Working on it',
         'starting printing' => 'Starting Printing',
         'packaging' => 'Packaging',
         'ready for delivery' => 'Ready For Delivery',
         'done' => 'Done',
         'finishing' => 'Finishing',
         'stuck' => 'Stuck',
    ];

        @endphp

<div>
<div class="flex gap-4 overflow-x-scroll w-full px-2 py-2">
@foreach($options as $key=>$option)
<a href="javascript:void(0)" class="status-print-kanban filament-stats-card relative p-3 rounded-md bg-white shadow dark:bg-gray-800 filament-stats-overview-widget-card " style="flex:1;

    opacity:{{$key == ($printStatus ?? '') ? '1' : '0.5'}}" data-status="{{$key}}">

<div class="space-y-2">
        <div class="flex items-center  rtl:space-x-reverse text-xs font-bold p-2 rounded-md" style="background-color:{{$statuses[$key]}}">
            <span> {{$option}} </span>
        </div>
        <div class="text-md font-bold">
        {{\App\Models\Order::where('print_house_status',$key)->count()}}
        </div>
    </div>
</a>
    @endforeach
</div>
    </div>
</x-filament::widget>
<style>
.filament-widgets-container{
    overflow-x:auto;
}
</style>
<script>
let status_print = document.querySelectorAll('.status-print-kanban');
let current_active = @js($printStatus ?? '');
status_print.forEach(elem =>{

    elem.addEventListener('click',(e)=>{
        let status = e.target.closest('a').getAttribute('data-status');
    if(current_active != status){
        let url = @js(url('/admin/orders?tableSortColumn=id&tableSortDirection=desc&tableFilters[print_house_status][value]='))+status;
        window.location.href = url
    }else{
        window.location.href = @js(url('/admin/orders?tableSortColumn=id&tableSortDirection=desc'));
    }
})
})
</script>
