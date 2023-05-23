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

<script src="https://unpkg.com/interactjs/dist/interact.min.js"></script>
