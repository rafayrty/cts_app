<x-forms::field-wrapper
:id="$getId()"
:label="$getLabel()"
:label-sr-only="$isLabelHidden()"
:helper-text="$getHelperText()"
:hint="$getHint()"
:hint-icon="$getHintIcon()"
:required="$isRequired()"
:state-path="$getStatePath()">
    @php
        $img_id = 'img-' . Str::random(20);
    @endphp
    <div x-data="
{
        state:$wire.entangle('{{ $getStatePath() }}').defer,
        init() {
document.querySelector('form').addEventListener('submit', function(event) {
    event.preventDefault();
});
   const debounce = (func, delay) => {
        let debounceTimer
        return function() {
            const context = this
            const args = arguments
                clearTimeout(debounceTimer)
                    debounceTimer
                = setTimeout(() => func.apply(context, args), delay)
                }
        }
        let self = this;
        let elems = document.querySelectorAll('.draggable-element-{{$img_id}}');
        elems.forEach(elem=>{

elem.addEventListener('paste', function(e) {
    // cancel paste
    e.preventDefault();

    // get text representation of clipboard
    var text = (e.originalEvent || e).clipboardData.getData('text/plain');

    // insert text manually
    document.execCommand('insertHTML', false, text);
});
          elem.addEventListener('input',debounce((e)=>{
            let data_id = e.target.getAttribute('data-id')
            self.state.predefined_texts[data_id]['value']['text'] = e.target.innerHTML;
          },1000))
        })
            interact('.draggable-element-{{$img_id}}')
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
                            var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
                            var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy
                            let data_id = target.getAttribute('data-id')

                            const valueX = (x / event.target.parentElement.clientWidth) * 100;
                            const valueY = (y / event.target.parentElement.clientHeight) * 100;
                          if(self.state.predefined_texts[data_id]){

                            self.state.predefined_texts[data_id]['value']['max_width'] = event.rect.width;
                            self.state.predefined_texts[data_id]['value']['width_percent'] = (event.rect.width / event.target.parentElement.clientWidth) * 100;

                            //Also update the X and Y Values as they are also effected by the width
                            self.state.predefined_texts[data_id]['value']['X_coord_percent'] = valueX;
                            self.state.predefined_texts[data_id]['value']['width_percent'] = (event.rect.width / event.target.parentElement.clientWidth) * 100;
                            self.state.predefined_texts[data_id]['value']['Y_coord_percent'] = valueY;

                            self.state.predefined_texts[data_id]['value']['X_coord'] = x;
                            self.state.predefined_texts[data_id]['value']['Y_coord'] = y;
                            console.log('should be updatig now')
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

                            //const valueX = (x / event.target.parentElement.clientWidth) * 100;
                            //const valueY = (y / event.target.parentElement.clientHeight) * 100;

                            const valueX = (x / event.target.parentElement.scrollWidth) * 100;
                            console.log(event.target.parentElement.clientHeight);
                            const valueY = (y / event.target.parentElement.clientHeight) * 100;
                          if(self.state.predefined_texts[data_id]){
                            self.state.predefined_texts[data_id]['value']['X_coord_percent'] = valueX;
                            self.state.predefined_texts[data_id]['value']['width_percent'] = (event.rect.width / event.target.parentElement.clientWidth) * 100;
                            self.state.predefined_texts[data_id]['value']['Y_coord_percent'] = valueY;
                            self.state.predefined_texts[data_id]['value']['X_coord'] = x;
                            self.state.predefined_texts[data_id]['value']['Y_coord'] = y;
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

              console.log('moving right now')
                target.style.transform = 'translate(' + x + 'px, ' + y + 'px)'

                // update the posiion attributes
                target.setAttribute('data-x', x)
                target.setAttribute('data-y', y)
            }
        }
}
    " id="{{ $img_id }}">
        @if (count($getData()) > 0)
        <h1 class="text-center font-semibold text-3xl my-4">Editing PDF Page # {{$getData()['page_number']+1}}</h1>
          @php
        $width = pt2px(json_decode($getData()['dimensions'],true)['width']);
        $height = pt2px(json_decode($getData()['dimensions'],true)['height']);
        @endphp
            <div id="editor-pdf" style="position:relative;overflow:auto;">
              <div style="width:{{$width}}px;height:{{$height}}px;margin:0 auto">
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
 }
 @endphp
  border:1px solid #000;
  background-color: {{$item['value']['bg_color']}};
  font-size:{{ $item['value']['font_size'] ? $item['value']['font_size'] . 'px' : 'auto' }};
  color:{{ $item['value']['color'] ? $item['value']['color'] : '#000' }};
  width:{{ $item['value']['max_width'] }}px;
  cursor: move;
  text-align:{{ $text_align }};
  position: absolute;
  direction:rtl;
  font-family:{{$item['value']['font_face']}};
  transform:translate({{ $item['value']['X_coord'] }}px,{{ $item['value']['Y_coord'] }}px)
  "
                        contenteditable="plaintext-only"
                        data-x={{$item['value']['X_coord']}}
                        data-font="{{$item['value']['font_face']}}"
                        data-y={{ $item['value']['Y_coord'] }}
                        class="draggable-element-{{$img_id}}"  data-id="{{ $key }}" >{{$item['value']['text'] }}</div>
                @endforeach
                <img draggable="false" style="
width:{{$width}}px;
height:{{$height}}px;
max-width:none;"
src="{{ $getData()['page'] }}" />
            </div>
            </div>
        @endif
    </div>
</x-forms::field-wrapper>
