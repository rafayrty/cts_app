<div>
    @php
$statuses = array(
"new order" => "rgba(0, 0, 255, 0.3)", // Blue
"starting printing" => "rgba(128, 0, 128, 0.3)", // Purple
"packaging" => "rgba(165, 42, 42, 0.3)", // Brown
"ready for delivery" => "rgba(0, 128, 128, 0.3)", // Teal
"in delivery" => "rgba(255, 165, 0, 0.3)", // Orange
"done" => "rgba(0, 128, 0, 0.3)", // Green
"stuck" => "rgba(255, 0, 0, 0.3)", // Red
"cancel" => "rgba(128, 128, 128, 0.3)" // Gray
);
$options = [
        'new order' => 'New Order',
         'starting printing' => 'Starting Printing',
         'packaging' => 'Packaging',
         'ready for delivery' => 'Ready For Delivery',
         'in delivery' => 'In Delivery',
         'done' => 'Done',
         'stuck' => 'Stuck',
         'cancel' => 'Cancel',
         ]
;
    @endphp
    @php
        $record = $getRecord();
    @endphp
    <select style="background:{{$statuses[$getState()]}}" id="client_status-{{$record->id}}" data-id="{{$record->id}}" class="client-status-switcher text-gray-900 block w-full transition duration-75 rounded-lg shadow-sm outline-none focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
            @foreach($options as $key => $value)
                <option value="{{$key}}" @if($getState()==$key) selected @endif>{{$value}}</option>
            @endforeach
    </select>
</div>

