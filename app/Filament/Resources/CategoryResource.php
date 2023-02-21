<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Actions\MakeSlug;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Product Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->afterStateUpdated(Closure::fromCallable(new MakeSlug()))->reactive()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->unique(ignorable: fn ($record) => $record)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Checkbox::make('featured')->label('Featured Category'),
                    Forms\Components\Textarea::make('description')
                        ->rows(5)
                        ->maxLength(255),
                    FileUpload::make('image')
                        ->required()
                        ->image()
                        ->directory('uploads'),
                ])->extraAttributes(['dir' => 'rtl']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
