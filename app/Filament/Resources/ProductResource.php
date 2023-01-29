<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Spatie\PdfToImage\Pdf as ConvertToImage;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static $pdfs = [];

    public static function form(Form $form): Form
    {
        $pdfs = self::$pdfs;

        return $form
            ->schema([
                Card::make()
                ->schema([Wizard::make([
                    Wizard\Step::make('Add Product')
                    ->schema([
                        TextInput::make('name')->placeholder('Enter Product Name')->required()->reactive()
                            ->afterStateUpdated(function (Closure $set, $state) {
                                $set('slug', make_slug($state));
                            }),
                        TextInput::make('slug')->placeholder('product-name')->required(),
                        TextInput::make('price')->placeholder('1.00')->numeric()->minValue(1)->required(),
                        RichEditor::make('description')->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ])->required(),
                        FileUpload::make('images')
                            ->image()
                            ->disk('s3')
                            ->required()
                            ->multiple(),
                    ]),
                    /* Step2 */
                    Wizard\Step::make('Add Documents')
                     ->schema([
                         Placeholder::make('Attach Documents to the products'),
                         Hidden::make('pdf_info'),
                         Repeater::make('Documents')
                             ->schema([
                                 Grid::make(2)
                                 ->schema([
                                     TextInput::make('name')->required()
                                     ->reactive()->afterStateUpdated(function (Closure $get, Closure $set, $state) {
                                         if ($get('../../pdf_info') != '') {
                                             $json_pdfs = json_decode($get('../../pdf_info'), true);
                                             $key = array_search($get('pdf_name'), array_column($json_pdfs, 'filename'));
                                             $json_pdfs[$key]['name'] = $state;
                                             $set('../../pdf_info', json_encode($json_pdfs));
                                         }
                                     }),
                                     Hidden::make('pdf_name'),
                                     Select::make('type')
                                         ->options([
                                             '1' => 'Cover',
                                             '2' => 'Book',
                                         ])->required()->reactive()
                                          ->afterStateUpdated(function (Closure $get, Closure $set, $state) {
                                              if ($get('../../pdf_info') != '') {
                                                  $json_pdfs = json_decode($get('../../pdf_info'), true);
                                                  $key = array_search($get('pdf_name'), array_column($json_pdfs, 'filename'));
                                                  $json_pdfs[$key]['type'] = $state;
                                                  $set('../../pdf_info', json_encode($json_pdfs));
                                              }
                                          }),
                                 ]),
                                 FileUpload::make('attatchment')
                                     ->acceptedFileTypes(['application/pdf'])
                                     ->helperText('File must be a pdf and must be smaller than 20mb')->maxSize(20 * 1024) //20MB
                                     ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (Closure $set, Closure $get, $state) use ($pdfs) {
                                        $name = $state->getClientOriginalName();
                                        $set('pdf_name', $name);
                                        $s3_file = Storage::disk('do')->get($state->path());
                                        $s3 = Storage::disk('local');
                                        $filename = 'temp/'.time().'.pdf';
                                        $s3->put($filename, $s3_file);
                                        $path = Storage::disk('local')->path($filename);
                                        $pdf = new ConvertToImage($path);
                                        $images = [];
                                        for ($i = 1; $i <= $pdf->getNumberOfPages(); $i++) {
                                            $img_path = 'uploads/'.time().'.jpg';
                                            $pdf->setPage($i)
                                                ->saveImage($img_path);
                                            $images[] = $img_path;
                                        }
                                        array_push($pdfs, ['filename' => $name, 'type' => $get('type'), 'name' => $get('name'), 'pdf' => $images]);
                                        $set('../../pdf_info', json_encode($pdfs));
                                        $set('../../../add_content', 'hey');
                                    }),
                             ]),
                     ]),
                    /* Step3 */
                    Wizard\Step::make('Add Content')
                            ->schema([
                                Placeholder::make('Add Content for Your Documents'),
                                Repeater::make('Pages')
                                    ->schema([
                                        Grid::make(2)
                                        ->schema([
                                            Select::make('document')
                                                ->options(function (Closure $get, Closure $set) {
                                                    if ($get('../../pdf_info') != '') {
                                                        $json_pdfs = json_decode($get('../../pdf_info'), true);
                                                        $array = [];
                                                        foreach ($json_pdfs as $pdf) {
                                                            $array[] = $pdf['name'];
                                                        }

                                                        return $array;
                                                    }
                                                }),
                                        ]),
                                        Placeholder::make('Info')
                                        ->content(new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">Please put this code {nadsoftText} in the position that will be modified by the user</h1>')),
                                        Textarea::make('predefined_text'),
                                        Grid::make(2)
                                        ->schema([
                                            TextInput::make('X_coord')->numeric()->minValue(1)->required(),
                                            TextInput::make('Y_coord')->numeric()->required(),
                                            ColorPicker::make('Color')->required(),
                                            TextInput::make('MaxWidth')->numeric()->required(),
                                            TextInput::make('FontSize')->numeric()->required(),
                                            Select::make('Text_Align')
                                                ->options([
                                                    'center' => 'Center',
                                                    'left' => 'Left',
                                                    'right' => 'Right',
                                                ])->required(),
                                        ]),
                                    ]),
                            ]),
                ])->startOnStep(2),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
