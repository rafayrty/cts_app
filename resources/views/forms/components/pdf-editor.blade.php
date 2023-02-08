<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>

<style>
@if(count($getData()) > 0)
  @php
    $text_align = 'right';
    if($getData()['text_align'] === 'C'){
      $text_align = "center";
    }else if($getData()['text_align'] === "L"){
      $text_align = "left";
    }else{
      $text_align = "right";
    }
  @endphp
/*.draggable-element{
  width: {{$getData()['width'] ? $getData()['width']."px" : 'auto'}};
  border:1px solid #000;
  background-color: #fff;
  font-size:{{$getData()['font_size'] ? $getData()['font_size']."px" : 'auto'}};
  color:{{$getData()['color'] ? $getData()['color'] : '#000'}};
  cursor: move;
  text-align:{{$text_align}};
  position: absolute;
  direction:rtl;
}*/
    @endif
</style>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer,modal }">
        <!-- Interact with the `state` property in Alpine.js -->
    <textarea wire:model="{{ $getStatePath() }}.predefined_text" placeholder="predefined_text" class="text-gray-900 block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600"></textarea>
    </div>
</x-dynamic-component>
<script>
document.addEventListener('livewire:load',function(){
  //Extract keys
  let str = @js($getStatePath());
  let page = str.substr( str.indexOf('.') + 1 );
  page = page.substr( page.indexOf('.') + 1 );
  page = page.substring(0, page.indexOf("."));
  console.log(@this['data']['Pages'][page]['pdf_editor'])
  @this['data']['Pages'][page]['pdf_editor']['predefined_text'] = "rafay"

  {{--var someValue = @this.@js($getStatePath()."predefined_text")--}}
})

</script>

