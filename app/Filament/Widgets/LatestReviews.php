<?php

namespace App\Filament\Widgets;

use App\Models\Review;
use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestReviews extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return Review::query()->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('product.demo_name'),
            Tables\Columns\TextColumn::make('user.email'),
            Tables\Columns\TextColumn::make('review'),
            Tables\Columns\TextColumn::make('stars'),
            Tables\Columns\IconColumn::make('status')
                ->boolean(),
        ];
    }
}
