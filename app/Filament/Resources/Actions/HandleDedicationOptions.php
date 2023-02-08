<?php

namespace App\Filament\Resources\Actions;

use App\Models\Dedication;
use Closure;

class HandleDedicationOptions
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
          return Dedication::all()->pluck('name','id');
    }
}
