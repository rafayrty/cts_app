<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class PDFEditor extends Field
{
    protected string $view = 'forms.components.p-d-f-editor';
    public array | Closure $options = [];

    //Define Model
    // ['predefined_texts'=>[],'page'=>'image_url','page_number'=>'','dimensions'=>'']
    public function options(array | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

   public function getOptions(): array
    {
        return $this->evaluate($this->options);
    }
}
