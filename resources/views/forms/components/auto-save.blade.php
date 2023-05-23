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
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer,
        id:null,
        init(){
          setInterval(()=>{
          let data = $wire.get('data')
          console.log(data,'heyy')
          if(data.slug){
            fetch('/save_product', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
              },
              body: this.id ? JSON.stringify({...data,product_id:this.id}) : JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                this.id = data.id;
            })
          }

          },5000)
        }
    }">
        <!-- Interact with the `state` property in Alpine.js -->
    </div>
</x-dynamic-component>
