<?php

namespace App\Filament\Resources;

use Beier\FilamentPages\Filament\Resources\FilamentPageResource;
use Beier\FilamentPages\Models\FilamentPage;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use RalphJSmit\Filament\SEO\SEO;

class CustomPageResource extends FilamentPageResource
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament-pages::filament-pages.filament.form.title.label'))
                    ->searchable()
                    ->sortable(),

                //TextColumn::make('slug')
                //->label(__('filament-pages::filament-pages.filament.form.slug.label'))
                //->icon('heroicon-o-external-link')
                //->iconPosition('after')
                //->sortable()
                //->toggleable(isToggledHiddenByDefault: false),
                BadgeColumn::make('status')
                    ->getStateUsing(fn (FilamentPage $record): string => $record->published_at->isPast() && ($record->published_until?->isFuture() ?? true) ? __('filament-pages::filament-pages.filament.table.status.published') : __('filament-pages::filament-pages.filament.table.status.draft'))
                    ->colors([
                        'success' => __('filament-pages::filament-pages.filament.table.status.published'),
                        'warning' => __('filament-pages::filament-pages.filament.table.status.draft'),
                    ]),

                TextColumn::make('published_at')
                    ->label(__('filament-pages::filament-pages.filament.form.published_at.label'))
                    ->date(__('filament-pages::filament-pages.filament.form.published_at.displayFormat')),
            ])
            ->filters([
                Filter::make('published_at')
                    ->form([
                        DatePicker::make('published_from')
                            ->label(__('filament-pages::filament-pages.filament.form.published_at.label'))
                            ->placeholder(fn ($state): string => '18. November '.now()->subYear()->format('Y')),
                        DatePicker::make('published_until')
                            ->label(__('filament-pages::filament-pages.filament.form.published_until.label'))
                            ->placeholder(fn ($state): string => now()->format('d. F Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['published_from'] ?? null) {
                            $indicators['published_from'] = 'Published from '.Carbon::parse($data['published_at'])->toFormattedDateString();
                        }
                        if ($data['published_until'] ?? null) {
                            $indicators['published_until'] = 'Published until '.Carbon::parse($data['published_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (FilamentPage $record) => $record->title == 'Home' ? true : false),
            ])
            ->bulkActions([
                //DeleteBulkAction::make(),
            ]);
    }

    public static function insertAfterSecondaryColumnSchema(): array
    {
        return [
            SEO::make(),
        ];
    }
}
