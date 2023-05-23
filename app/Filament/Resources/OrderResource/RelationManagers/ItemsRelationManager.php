<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'barcode_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('barcode_number')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->getStateUsing(function (Model $record) {
                    $item = $record;
                    $inputs = $item->inputs;
                    $product = Product::find($item->product_id);

                    if ($product) {
                        $name = $inputs['name'];

                        return str_replace('{basmti}', $name, $product->demo_name);
                    } else {
                        return $item->name;
                    }
                })->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                //->disk('do'),
                Tables\Columns\TextColumn::make('cover.name'),
                Tables\Columns\TextColumn::make('cover.price')->label('Cover Price')->money('ils'),
                Tables\Columns\TextColumn::make('discount_total')->label('Discount')->money('ils'),

                //ViewColumn::make('barcode_number')->view('filament.columns.barcode'),
                Tables\Columns\TextColumn::make('barcodes')->getStateUsing(function (Model $record) {
                    $order = $record->order;
                    $barcodes = [];
                    foreach ($order->barcodes as $barcode) {
                        $parts = explode('-', $barcode['barcode_number']);
                        $last_num = end($parts);
                        if ($last_num == $record->id) {
                            $barcodes[] = $barcode['barcode_number'];
                        }
                    }

                    return implode(',', $barcodes);
                }),
                Tables\Columns\TextColumn::make('price')->money('ils')->sortable(),
                Tables\Columns\TextColumn::make('total')->money('ils'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public function getPages(): array
    {
        return [
        ];
    }
}
