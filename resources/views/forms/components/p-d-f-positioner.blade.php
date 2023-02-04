<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div>
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
#draggable-element {
  width: {{$getData()['width'] ? $getData()['width']."px" : 'auto'}};
  border:1px solid #000;
  background-color: #fff;
  font-size:{{$getData()['font_size'] ? $getData()['font_size']."px" : 'auto'}};
  color:{{$getData()['color'] ? $getData()['color'] : '#000'}};
  cursor: move;
  text-align:{{$text_align}};
  position: absolute;
  direction:rtl;
  /* important (all position that's not `static`) */
}
    @endif
</style>
  @php
    $img_id = Str::random(20);
  @endphp
      <h1 class="text-center font-semibold text-3xl my-4">Editing PDF Page</h1>

      @if(count($getData()) > 0)
        <div id="editor-pdf" style="position:relative;">

        @if($getData()['predefined_text'])
            <div ondblclick="this.contentEditable=true;this.className='inEdit';" onblur="this.contentEditable=false;this.className='';" contenteditable="false" id="draggable-element" dir="rtl" >{!!nl2br($getData()['predefined_text'])!!}</div>
        @endif
      <img src="{{$getData()['page']}}" />
        </div>
    @endif
      {{--{{var_dump($getData())}}--}}
    </div>
</x-forms::field-wrapper>

<script src="https://unpkg.com/interactjs/dist/interact.min.js"></script>
<script>
    document.addEventListener('livewire:load', function () {
        // Get the value of the "count" property
        // Set the value of the "count" property
    @this.increment();
      console.log(@js($count),"count")
    })

</script>


@if(count($getData()) > 0 && $getData()['predefined_text'])
<script>
// target elements with the "draggable" class
interact('#draggable-element')
  .resizable({
    // resize from all edges and corners
    edges: { left: true, right: true, bottom: false, top: false },

    listeners: {
      move (event) {
        var target = event.target
        var x = (parseFloat(target.getAttribute('data-x')) || 0)
        var y = (parseFloat(target.getAttribute('data-y')) || 0)

        // update the element's style
        target.style.width = event.rect.width + 'px'
        target.style.height = event.rect.height + 'px'

        // translate when resizing from top or left edges
        x += event.deltaRect.left
        y += event.deltaRect.top

        target.style.transform = 'translate(' + x + 'px,' + y + 'px)'

        target.setAttribute('data-x', x)
        target.setAttribute('data-y', y)
      }
    },
    modifiers: [
      // keep the edges inside the parent
      interact.modifiers.restrictEdges({
        outer: 'parent'
      }),

      // minimum size
      interact.modifiers.restrictSize({
        min: { width: 100, height: 50 }
      })
    ],

    inertia: true
  })
  .draggable({
    // enable inertial throwing
    inertia: true,
    // keep the element within the area of it's parent
    modifiers: [
      interact.modifiers.restrictRect({
        restriction: 'parent',
      })
    ],
    // enable autoScroll
    autoScroll: true,

    listeners: {
      // call this function on every dragmove event
      move: dragMoveListener,

      // call this function on every dragend event
      end (event) {
        var textEl = event.target.querySelector('p')

        textEl && (textEl.textContent =
          'moved a distance of ' +
          (Math.sqrt(Math.pow(event.pageX - event.x0, 2) +
                     Math.pow(event.pageY - event.y0, 2) | 0))
            .toFixed(2) + 'px')
      }
    }
  })

function dragMoveListener (event) {
  var target = event.target
  // keep the dragged position in the data-x/data-y attributes
  var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
  var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy

  console.log(@js($pdf_data),"pdf_data")
  // translate the element
  //update state
  target.style.transform = 'translate(' + x + 'px, ' + y + 'px)'

  // update the posiion attributes
  target.setAttribute('data-x', x)
  target.setAttribute('data-y', y)
}
</script>
@endif
