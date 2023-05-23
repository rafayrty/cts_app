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
  :state-path="$getStatePath()">
    @php
        $img_id = 'img-' . Str::random(20);
    @endphp
<style>
#editor-pdf{
  position:relative;
  overflow:auto;
}
table td{
  padding:0 .5rem;
}
table th{
  text-align:center;
  font-size:.8rem;
  margin-bottom:.8rem;
}
table tr{
  margin-bottom:.5rem;
}
</style>
    <div x-data="{
        state:$wire.entangle('{{ $getStatePath() }}').defer,
        init(){
          //console.log(this.state);
          this.setup_editor();
        },
        img_id:'draggable-element-editor-{{$img_id}}',
           getAlignment(alignment){
              if(alignment == 'R') return 'right';
              if(alignment == 'C') return 'center';
              if(alignment == 'L') return 'left';
           },
           update_state(){
            this.state = {...this.state,predefined_texts:this.fields}
           },
           setup_editor(){

            let self = this;
            interact('.draggable-element-editor-{{$img_id}}')
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

                            let index = target.getAttribute('data-id')
                            //If Cover
                            const multiplier = self.state.type != 2 ? 2 : 1;
                            const valueX = (x / (event.target.parentElement.clientWidth * multiplier)) * 100;
                            const valueY = (y / event.target.parentElement.clientHeight) * 100;
                        if(self.fields.hasOwnProperty(index)){
                            self.fields[index].X_coord = x;
                            self.fields[index].Y_coord = y;

                            self.fields[index].X_coord_percent = valueX;
                            self.fields[index].Y_coord_percent = valueY;

                            self.fields[index].width_percent = (event.rect.width / event.target.parentElement.clientWidth) * 100;
                            self.fields[index].max_width = event.rect.width;
                            self.update_state()
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
                            //elementRect: { top: -4.4, left: -1.13, bottom: -4.4, right: -1.13 },
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
                            let index = target.getAttribute('data-id')

                            //If Cover
                            const multiplier = self.state.type != 2 ? 2 : 1;
                            console.log(event.rect.width,'rect width')
                            const valueX = (x / (event.target.parentElement.clientWidth * multiplier)) * 100;
                            const valueY = (y / event.target.parentElement.clientHeight) * 100;

                        //if(self.fields.hasOwnProperty(index)){

                            self.fields[index].X_coord = x;
                            self.fields[index].Y_coord = y;

                            self.fields[index].X_coord_percent = valueX;
                            self.fields[index].Y_coord_percent = valueY;

                            self.fields[index].width_percent = (event.rect.width / event.target.parentElement.clientWidth) * 100;
                            self.fields[index].max_width = event.rect.width;
                            self.update_state()
                        //}

                        }

                    }
                })

            function dragMoveListener(event) {
                var target = event.target
                // keep the dragged position in the data-x/data-y attributes
                var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
                var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy

                const valueX = (x / event.target.parentElement.clientWidth) * 100;
                const valueY = (y / event.target.parentElement.clientHeight) * 100;
                target.style.transform = 'translate(' + x + 'px, ' + y + 'px)'
                // update the posiion attributes
                target.setAttribute('data-x', x)
                target.setAttribute('data-y', y)
            }
           }
}">
<div x-data="{
        fields:[],
        init(){
        let self = this;
          $watch('state', (value,oldvalue) => {
          if(value == undefined){
              self.fields = [];
              self.setup_editor()
          }else{
          console.log('should not be called')
            value.predefined_texts.forEach(text=>console.log(text))
            value?.predefined_texts?.forEach((text,index)=>{
           if(text.font_face != oldvalue.predefined_texts[index].font_face){
                  self.fields = [];
                  self.init();
            }
          })
        }
          })
            if(this.state){
              if(this.state.predefined_texts){
                  this.state.predefined_texts.forEach((txt)=>{
                    this.fields.push(txt)
                  })
                  this.setup_editor();
              }
            }
          },
        update_text(text,index){
            this.fields[index].text =text;
            this.update_state()
        },
        addNewField() {
            this.fields.push({
                X_coord: 114,
                Y_coord: 114,
                X_coord_percent: 0,
                Y_coord_percent: 0,
                text: 'أدخل النص',
                color: '#000',
                bg_color: '#000',
                max_width: 100,
                text_align:'R',
                width_percent: 0,
                line_height: 1.5,
                font_size:16,
                font_face:'GE-Dinar-Medium',
             });

              this.setup_editor();
             //this.fields.forEach((e,index)=>this.setup_editor())
          },
          removeField(index) {
             this.fields.splice(index, 1);
             this.fields.forEach((e,index)=>this.setup_editor())
             console.log(index,'index')
             this.update_state()
           },
}">
      <div>
        @if (count($getData()) > 0)
<table class="table table-bordered align-items-center table-sm">
  <thead class="thead-light">
   <tr>
     <th>#</th>
     <th>X-Coord</th>
     <th>Y-Coord</th>
     <th>MaxWidth</th>
     <th>Color</th>
     <th>BGColor</th>
     <th>FontSize</th>
     <th>FontFace</th>
     <th>Textalign</th>
     <th>Line-Height</th>
     <th></th>
    </tr>
  </thead>
  <tbody >
    <template  x-if="fields != undefined">
    <template x-for="(field, index) in fields" :key="index">
     <tr>
      <td x-text="index + 1"></td>
      <td>
        <input x-model="field.X_coord" step="any" type="number" name="X_coord[]" class="form-control block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
      </td>
      <td>
        <input x-model="field.Y_coord"  step="any" type="number" name="Y_coord[]" class="form-control block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
      </td>
      <td>
        <input x-model="field.max_width" step="any" type="number" name="max_width[]" class="form-control block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
      </td>
      <td width="100">
        <input x-model="field.color" type="color" name="color[]" class="form-control block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
      </td>
      <td width="100">
        <input x-model="field.bg_color" type="color" name="bg_color[]" class="form-control block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
      </td>
      <td>
        <input x-model="field.font_size" type="number"  name="font_size[]" class="form-control block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
      </td>
      <td width="200">
        <select x-model="field.font_face" type="font_face"  name="font_face[]" class="text-gray-900 block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
          <option selected value="GE-Dinar-Medium">
            GE-Dinar-Medium
          </option>
          @foreach (\App\Models\Fonts::all() as $item)
            <option value="{{$item->font_name}}">{{$item->font_name}}</option>
          @endforeach
        </select>
      </td>
      <td width="120">
        <select x-model="field.text_align" name="text_align[]" class="text-gray-900 block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
          <option value="R">Right</option>
          <option value="C">Center</option>
          <option value="L">Left</option>
        </select>
      </td>
      <td>
        <input x-model="field.line_height" step=".01" type="number" name="line_height[]" class="form-control block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
      </td>
      <td width="6">
        <button title="Delete" @click="removeField(index)" type="button" class="flex items-center justify-center flex-none w-4 h-4 text-danger-600 transition hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400">
            <span class="sr-only">
                Delete
            </span>
<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
</svg>
        </button>
      </td>
    </tr>
   </template>
   </template>
  </tbody>
</table>
<div class="add-btn flex flex-col justify-center items-center mt-4">
<button type="button" class="text-center filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
  @click="addNewField()">+ Add Text</button>
</div>
      </div>
        <!-- If There are Options -->
            <h1 class="text-center font-semibold text-3xl my-4">Editing PDF Page # {{ $getData()['page_number'] + 1 }}
            </h1>
            @php
                $width = pt2px(json_decode($getData()['dimensions'], true)['width']);
                $height = pt2px(json_decode($getData()['dimensions'], true)['height']);
            @endphp

        <div id="editor-pdf">
                <div style="width:{{ $width }}px;height:{{ $height }}px;margin:0 auto">
                      {{--  Image Of The Page  --}}
                        <template x-if="fields != undefined">
                        <template x-for="(field, index) in fields" :key="index">
                          <div
                            :class="img_id"
                            contenteditable="plaintext-only"
                            dir="rtl"
                            :style="{
                              border:'1px solid #000',
                              position:'absolute',
                              textAlign:getAlignment(field.text_align),
                              color:field.color,
                              fontFamily:field.font_face,
                              fontSize:field.font_size+'px',
                              lineHeight:field.line_height,
                              width:field.max_width+'px',
                              transform:`translate(${field.X_coord}px,${field.Y_coord}px)`
                            }"
                            :data-id="index"
                            :data-x="field.X_coord"
                            :data-y="field.Y_coord"
                            {{--@input="field.text = $event.target.textContent"--}}
                            @input.debounce.3000="update_text($event.target.textContent,index)" x-text="field.text"></div>
                        </template>
                      </template>
                    <img draggable="false"
                        loading="lazy"
                        style="height:100%;width:100%;max-width:none;"
                        src="{{ $getData()['page'] }}" />
                </div>
            </div>
        @endif
    </div>
  </div>
</x-dynamic-component>

