<?php

namespace App\Http\Livewire;

use Livewire\Component;

//namespace Filament\Forms\Components;

use Closure;
use \Filament\Support\Concerns\HasExtraAlpineAttributes;

//class ColorPicker extends \Filament\Forms\Components\Field
class ColorPicker extends Component
{
    //use \Filament\Forms\Components\Concerns\HasAffixes;
    //use \Filament\Forms\Components\Concerns\HasExtraInputAttributes;
    //use \Filament\Forms\Components\Concerns\HasPlaceholder;
    //use HasExtraAlpineAttributes;

    protected string $view = 'livewire.color-picker';

    public $value = '';
    protected string | Closure $format = 'hex';

    public function format(string | Closure $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function hex(): static
    {
        $this->format('hex');

        return $this;
    }

    public function hsl(): static
    {
        $this->format('hsl');

        return $this;
    }

    public function rgb(): static
    {
        $this->format('rgb');

        return $this;
    }

    public function rgba(): static
    {
        $this->format('rgba');

        return $this;
    }

    public function getFormat(): string
    {
        return $this->evaluate($this->format);
    }
}
