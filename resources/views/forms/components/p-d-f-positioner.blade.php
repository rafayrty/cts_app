<x-forms::field-wrapper :id="$getId()" :label="$getLabel()" :label-sr-only="$isLabelHidden()" :helper-text="$getHelperText()" :hint="$getHint()"
    :hint-icon="$getHintIcon()" :required="$isRequired()" :state-path="$getStatePath()">
    @php
        $img_id = 'img-' . Str::random(20);
    @endphp
    <div x-data="
{
        state:$wire.entangle('{{ $getStatePath() }}'),
        init() {
        console.log($wire.get(`{{$getStatePath()}}.predefined_texts[data_id][0]['max_width']`));
            let self = this;
            interact('.draggable-element')
                .resizable({
                    // resize from all edges and corners
                    edges: { left: true, right: true, bottom: false, top: false },

                    listeners: {
                        move(event) {
                            var target = event.target
                            var x = (parseFloat(target.getAttribute('data-x')) || 0)
                            var y = (parseFloat(target.getAttribute('data-y')) || 0)


                            // update the element's style
                            target.style.width = event.rect.width + 'px'

                            // translate when resizing from top or left edges
                            x += event.deltaRect.left
                            y += event.deltaRect.top

                            target.style.transform = 'translate(' + x + 'px,' + y + 'px)'
                         target.setAttribute('data-x', x)
                          target.setAttribute('data-y', y)
                        },

                        end(event) {
                            var target = event.target
                          console.log(self.state);
                          let data_id = target.getAttribute('data-id')
                          if(self.state.predefined_texts[data_id]){
                            //console.log($wire.get(`{{$getStatePath()}}.predefined_texts.${data_id}.value.max_width`))
                            self.state.predefined_texts[data_id]['value']['max_width'] = event.rect.width;
                            self.state.predefined_texts[data_id]['value']['width_percent'] = (event.rect.width / event.target.parentElement.clientWidth) * 100;
                          }
                        }
                    },
                    modifiers: [
                        // keep the edges inside the parent
                        interact.modifiers.restrictEdges({
                            outer: 'parent'
                        }),

                        // minimum size
                        interact.modifiers.restrictSize({
                            min: { width: 100 }
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
                        end(event) {

                            var target = event.target
                            var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
                            var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy
                            let data_id = target.getAttribute('data-id')

                            const valueX = (x / event.target.parentElement.clientWidth) * 100;
                            const valueY = (y / event.target.parentElement.clientHeight) * 100;
                            const screen_size = {height:window.innerHeight,width:window.innerWidth}
                          console.log(self.state);
                          if(self.state.predefined_texts[data_id]){

                            self.state.predefined_texts[data_id]['value']['screen_size'] = screen_size;
                            self.state.predefined_texts[data_id]['value']['X_coord_percent'] = valueX;

                            self.state.predefined_texts[data_id]['value']['width_percent'] = (event.rect.width / event.target.parentElement.clientWidth) * 100;
                            self.state.predefined_texts[data_id]['value']['Y_coord_percent'] = valueY;
                            self.state.predefined_texts[data_id]['value']['X_coord'] = x;
                            self.state.predefined_texts[data_id]['value']['Y_coord'] = y;
//                           self.state.predefined_texts[data_id]['value']['Y_coord'] = y;
//                            self.state.predefined_texts[data_id]['value']['X_coord'] = x;

                            setTimeout(function() {
                                target.focus()
                            }, 0);
                          }
                        }

                    }
                })

            function dragMoveListener(event) {
                var target = event.target
                // keep the dragged position in the data-x/data-y attributes
                var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
                var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy
                // translate the element
                //update state

                const valueX = (x / event.target.parentElement.clientWidth) * 100;
                const valueY = (y / event.target.parentElement.clientHeight) * 100;

                target.style.transform = 'translate(' + x + 'px, ' + y + 'px)'

                // update the posiion attributes
                target.setAttribute('data-x', x)
                target.setAttribute('data-y', y)
            }
        }
}
    " id="{{ $img_id }}">
        <h1 class="text-center font-semibold text-3xl my-4">Editing PDF Page</h1>
        @if (count($getData()) > 0)
            <div id="editor-pdf" style="position:relative;">
                @foreach ($getData()['predefined_texts'] as $key => $item)
                    <div style="
  @php
$text_align = 'right';
    if($item['value']['text_align'] === 'C'){
      $text_align = "center";
    }else if($item['value']['text_align'] === "L"){
      $text_align = "left";
    }else{
      $text_align = "right";
    } @endphp
  border:1px solid #000;
  background-color: {{$item['value']['bg_color']}};
  font-size:{{ $item['value']['font_size'] ? $item['value']['font_size'] . 'px' : 'auto' }};
  color:{{ $item['value']['color'] ? $item['value']['color'] : '#000' }};
  width:{{ $item['value']['max_width'] }}px;
  cursor: move;
  text-align:{{ $text_align }};
  position: absolute;
  direction:rtl;
  transform:translate({{ $item['value']['X_coord'] }}px,{{ $item['value']['Y_coord'] }}px)
              "
                        x-on:input.debounce.1000ms="state.predefined_texts[{{ $key }}]['value']['text'] = $event.target.innerHTML"
                        contenteditable="true"
                        {{-- ondblclick="this.contentEditable=true;this.className='inEdit';" onblur="this.contentEditable=false;this.className='';" contenteditable="false"<] --}} data-x={{ $item['value']['X_coord'] }}
                        data-y={{ $item['value']['Y_coord'] }}
                        class="draggable-element" data-id="{{ $key }}">
                        {!! $item['value']['text'] !!}
                    </div>
                @endforeach
                <img draggable="false" src="{{ $getData()['page'] }}" />
            </div>
        @endif
    </div>
</x-forms::field-wrapper>
<script src="https://unpkg.com/interactjs/dist/interact.min.js"></script>

<script>

    document.addEventListener('alpine:init', () => {
    Alpine.data('pdf_positioner',(initialState={})=>({

}))

})
</script>
