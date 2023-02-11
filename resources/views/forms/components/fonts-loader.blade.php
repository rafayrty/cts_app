@php
$fonts = \App\Models\Fonts::all();
@endphp
<style>
@foreach($fonts as $font)
  @fontface{
    font-family: {{$font->font_name}};
    src: url({{ Storage::path('public/'.$font->attatchment) }});
  }
@endforeach
  @fontface{
    font-family: NotoSansArabic-Regular;
    src: url({{ public_path('fonts/NotoSansArabic-Regular.ttf') }});
  }
</style>

<h1>Hey</h1>
