<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class PDFDedicationEditor extends Field
{
    protected string $view = 'forms.components.p-d-f-dedication-editor';

    public array|Closure $data = [];

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
