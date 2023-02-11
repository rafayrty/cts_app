<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class PdfDedicationPositioner extends Field
{
    protected string $view = 'forms.components.pdf-dedication-positioner';

    public array|Closure $pdf_data = [];

    public function update_width()
    {
        $this->pdf_data = null;
    }

    public function update_pdf_data($data)
    {
        //dd($data);
    }

    public function set_pdf_data(array|Closure $pdf_data): static
    {
        $this->pdf_data = $pdf_data;

        return $this;
    }

    public function getData(): array
    {
        return $this->evaluate($this->pdf_data);
    }
}
