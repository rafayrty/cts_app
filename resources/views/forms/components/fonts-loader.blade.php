@php
$fonts = \App\Models\Fonts::all();
@endphp
<style>
/*@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@600&display=swap');*/
@font-face{
    font-family: GE-Dinar-Medium;
    src: url('{{ asset('fonts/GE-Dinar-One-Medium.ttf') }}');
}
@foreach($fonts as $font)
  @font-face{
    font-family: {{$font->font_name}};
    src: url('{{'/storage/'.$font->attatchment}}');
  }
@endforeach
</style>
<script>
// Add a 'wheel' event listener to the active element
let activeElement = document.querySelectorAll('input');
//document.body.style.overflow = 'hidden'
setInterval(()=>{
    activeElements = document.querySelectorAll('input');
    activeElements.forEach(elem=>{
        elem.addEventListener('change', ()=> document.body.style.overflow = 'hidden', { passive: false })
        elem.addEventListener('blur', ()=>{     document.body.style.overflow = 'auto'})
    });
},1000)
//document.addEventListener('scroll',(e)=>console.log("scroll called",e))
</script>
<script src="/interact.min.js"></script>

