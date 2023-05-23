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
<div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }" style="margin-bottom:.5rem;">
@php
$order = \App\Models\Order::where('order_numeric_id',$evaluate(fn ($get) => $get('order_numeric_id')))->get()->first();
@endphp
<div x-data="{
timer:null,
init(){
  setInterval(()=>{
    this.timeDifference(new Date(),new Date('{{$order->created_at}}'))
  },1000)
},
timeDifference(now,startDate){
    const elapsed = now - startDate;
    const totalSeconds = Math.floor(elapsed / 1000);
    const days = Math.floor(totalSeconds / 86400);
    const hours = Math.floor((totalSeconds % 86400) / 3600);
    const minutes = Math.floor(((totalSeconds % 86400) % 3600) / 60);
    const seconds = ((totalSeconds % 86400) % 3600) % 60;
    this.timer = `${days}d ${hours}h ${minutes}m ${seconds}s`;
}
}"
  class="filament-forms-card-component p-6 bg-white rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800">
  <p>Time Since Order Was Placed:</p>
 <span x-text="timer" class="text-lg font-semibold"></span>
</div>
</x-dynamic-component>

