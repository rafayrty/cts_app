<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class PDFEditor extends Field
{
    protected string $view = 'forms.components.p-d-f-editor';

    public array|Closure $data = [];

    //Define Model
    // ['predefined_texts'=>[],'page'=>'image_url','page_number'=>'','dimensions'=>'']

    public function set_pdf_data(array|Closure $data): static
    {
        $this->data = $data;

        return $this;
    }

   public function getData(): array
   {
       return $this->evaluate($this->data);
   }

}
