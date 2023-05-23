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
$form_submission = \App\Models\FormSubmissions::where('id',$evaluate(fn ($get) => $get('id')))->get()->first();
@endphp
  <p class="font-semibold text-2xl">Form Submission Details</p>
      <ul class="order-info-list mt-4">
        @foreach ($form_submission->content as $key =>$value)
          <li class="mt-2"><strong>{{$key}}:</strong> {{$value}}</li>
        @endforeach
      </ul>
</x-dynamic-component>

