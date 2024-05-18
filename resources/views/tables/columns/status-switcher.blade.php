<div>
    @php
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
    @php
        $record = $getRecord();
    @endphp
    <select id="print_status-{{$record->id}}" style="background:{{$statuses[$getState()]}}" data-id="{{$record->id}}" class="print-status-switcher text-gray-900 block w-full transition duration-75 rounded-lg shadow-sm outline-none focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
            @foreach($options as $key => $value)
                <option value="{{$key}}" @if($getState()==$key) selected @endif>{{$value}}</option>
            @endforeach
    </select>
</div>

