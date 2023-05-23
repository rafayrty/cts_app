<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Actions\MakeSlug;
use App\Filament\Resources\ProductAttributeResource\Pages;
use App\Models\ProductAttribute;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ProductAttributeResource extends Resource
{
    protected static ?string $model = ProductAttribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Product Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('name')->placeholder('Enter Attribute Name')
                        ->required()
                        ->maxLength(255),
                    Repeater::make('options')
                        ->relationship('options')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('name')->required()->placeholder('Enter Attibute Option Name')
                                ->maxLength(255)
                                ->afterStateUpdated(Closure::fromCallable(new MakeSlug()))->reactive(),
                                TextInput::make('slug')->required()->maxLength(255),
                                Textarea::make('description')
                                        ->rows(5)
                                        ->maxLength(255),
                                FileUpload::make('image')
                                    ->image()
                                    //->disk('do')
                                    ->directory('uploads'),
                            ]),
                        ]),
                    //])->minItems(fn (ProductAttribute $record) => $record->name == 'الجنس' ? 2 : 2)->maxItems(fn (ProductAttribute $record) => $record->name == 'الجنس' ? 2 : 40),
                ])->extraAttributes(['dir' => 'rtl']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('options.name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->hidden(),
                Tables\Actions\DeleteAction::make()->hidden(fn (ProductAttribute $record) => $record->name == 'الجنس' ? true : false),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProductAttributes::route('/'),
            'create' => Pages\CreateProductAttribute::route('/create'),
            'edit' => Pages\EditProductAttribute::route('/{record}/edit'),
        ];
    }
}
