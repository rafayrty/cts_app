<?php

namespace App\Filament\Resources\Actions;

use Closure;

class MakeSlug
{
    public function __invoke(Closure $set, $state)
    {
        if ($state != make_slug($state)) {
            $set('slug', make_slug($state));
        }
    }
}
