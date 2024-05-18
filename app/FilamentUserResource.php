<?php

declare(strict_types=1);

namespace App;

use Carbon\Carbon;
use Chiiya\FilamentAccessControl\Enumerators\Feature;
use Chiiya\FilamentAccessControl\Fields\PermissionGroup;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Chiiya\FilamentAccessControl\Resources\FilamentUserResource\Pages\CreateFilamentUser;
use Chiiya\FilamentAccessControl\Resources\FilamentUserResource\Pages\EditFilamentUser;
use Chiiya\FilamentAccessControl\Resources\FilamentUserResource\Pages\ListFilamentUsers;
use Chiiya\FilamentAccessControl\Resources\FilamentUserResource\Pages\ViewFilamentUser;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class FilamentUserResource extends Resource
{
    protected static ?string $model = FilamentUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                Grid::make()
                    ->schema(
                        fn (Component $livewire) => $livewire instanceof ViewFilamentUser
                    ? [
                        self::detailsSection(),
                        Section::make(__('filament-access-control::default.sections.permissions'))
                            ->description(__('filament-access-control::default.messages.permissions_view'))
                            ->schema([
                                PermissionGroup::make('permissions')
                                    ->label(__('filament-access-control::default.fields.permissions'))
                                    ->validationAttribute(__('filament-access-control::default.fields.permissions'))
                                    ->resolveStateUsing(
                                        fn (FilamentUser $record) => $record->getAllPermissions()->pluck('id')->all(),
                                    ),
                            ]),
                    ] : [
                        self::detailsSection(),
                        Section::make(__('filament-access-control::default.sections.permissions'))
                            ->description(__('filament-access-control::default.messages.permissions_create'))
                            ->schema([
                                PermissionGroup::make('permissions')
                                    ->label(__('filament-access-control::default.fields.permissions'))
                                    ->validationAttribute(__('filament-access-control::default.fields.permissions')),
                            ]),
                    ],
                    )
                    ->columns(1),
            );
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('full_name')
                ->label(__('filament-access-control::default.fields.full_name'))
                ->searchable(['first_name', 'last_name']),
            TextColumn::make('email')
                ->label(__('filament-access-control::default.fields.email'))
                ->searchable(),
            TextColumn::make('role')
                ->label(__('filament-access-control::default.fields.role'))
                ->getStateUsing(fn (FilamentUser $record) => __(optional($record->roles->first())->name)),
            ...(
                Feature::enabled(Feature::ACCOUNT_EXPIRY)
                ? [
                    BooleanColumn::make('active')
                        ->label(__('filament-access-control::default.fields.active'))
                        ->getStateUsing(fn (FilamentUser $record) => ! $record->isExpired()),
                ]
                : []
            ),
        ])
            ->prependBulkActions([
                ...(
                    Feature::enabled(Feature::ACCOUNT_EXPIRY)
                    ? [
                        BulkAction::make('extend')
                            ->label(__('filament-access-control::default.actions.extend'))
                            ->action('extendUsers')
                            ->requiresConfirmation()
                            ->deselectRecordsAfterCompletion()
                            ->color('success')
                            ->icon('heroicon-o-clock'),
                    ]
                    : []
                ),
            ])
            ->filters([
                ...(
                    Feature::enabled(Feature::ACCOUNT_EXPIRY)
                    ? [
                        Filter::make(__('filament-access-control::default.filters.expired'))
                            ->query(
                                fn (Builder $query) => $query->whereNotNull(
                                    'expires_at',
                                )->where('expires_at', '<=', now()),
                            ),
                    ]
                    : []
                ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFilamentUsers::route('/'),
            'create' => CreateFilamentUser::route('/create'),
            'edit' => EditFilamentUser::route('/{record}/edit'),
            'view' => ViewFilamentUser::route('/{record}'),
        ];
    }

    public static function getLabel(): string
    {
        return __('filament-access-control::default.resources.admin_user');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-access-control::default.resources.admin_users');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament-access-control::default.resources.group');
    }

    protected static function evaluateMinDate(Component $livewire): ?Carbon
    {
        if ($livewire instanceof CreateFilamentUser) {
            return now();
        }

        return null;
    }
}
