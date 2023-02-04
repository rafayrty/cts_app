<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Forms\Components\PDFPositioner;
use App\Models\Product;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
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
                        Placeholder::make('Info')
                        ->content(new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">Please put this code {basmti} in the position that will be modified by the user</h1>')),
                        TextInput::make('demo_name')->placeholder('Enter Product Name With the {name}')->required(),
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
                                     ->helperText('File must be a pdf and must be smaller than 500mb')->maxSize(500 * 1024) //20MB
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
                                        //Set barcode Location
                                        if (count($pdfs) > 1) {
                                            $location = ['first' => 'First Page', 'last' => 'Last Page', 'both' => 'Both First & Last'];
                                            $set('barcode_settings.location', $location);
                                        } else {
                                            $location = ['first' => 'First Page', 'last' => 'Last Page'];
                                            $set('barcode_settings.location', $location);
                                        }
                                    }),
                                 Section::make('Barcode Settings')->schema([

                                     Grid::make(2)
                                        ->schema([
                                            Radio::make('barcode_position')
                                               ->options([
                                                   'top_left' => 'Top Left',
                                                   'top_right' => 'Top Right',
                                                   'bottom_left' => 'Bottom Left',
                                                   'bottom_right' => 'Bottom Right',
                                               ])->required(),
                                            Select::make('location')
                                            ->options(function (Closure $get, Closure $set) {
                                                if ($get('../../pdf_info') != '') {
                                                    $json_pdfs = json_decode($get('../../pdf_info'),true);
                                                    if(count($json_pdfs) > 1){
                                                        return ['first'=>"First Page",'last'=>"Last Page",'both'=>"Both Page"];
                                                    }else{
                                                        return ['first'=>"First Page"];
                                                    }
                                                }
                                            })->required()->reactive(),
                                        ]),
                                 ])->compact(),
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
                                            Select::make('page')
                                                ->options(function (Closure $get, Closure $set) {
                                                    if ($get('../../pdf_info') != '') {
                                                        $json_pdfs = json_decode($get('../../pdf_info'), true);
                                                        $key = array_search($get('pdf_name'), array_column($json_pdfs, 'filename'));
                                                        $pages_count = count($json_pdfs[$key]['pdf']);
                                                        $array = [];
                                                        for ($i = 1; $i <= $pages_count; $i++) {
                                                            $array[] = $i;
                                                        }

                                                        return $array;
                                                    }
                                                })
                                                ->disabled(function (Closure $set, Closure $get, $state) {
                                                    if ($get('document') != '' && $get('../../pdf_info') != '') {
                                                        return false;
                                                    }

                                                    return true;
                                                })->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                                                    if ($get('document') != '' && $get('../../pdf_info') != '') {
                                                        $json_pdfs = json_decode($get('../../pdf_info'), true);
                                                        $key = array_search($get('document'), array_column($json_pdfs, 'filename'));
                                                        $json_pdfs[$key]['type'] = $state;
                                                        $img_page = (int) $get('page');
                                                        $set('image', [
                                                            'predefined_text' => $get('predefined_text'),
                                                            'image' => asset($json_pdfs[0]['pdf'][$img_page]),
                                                            'text_align' => $get('text_align')]);
                                                    }
                                                })->reactive()->required(),
                                            Select::make('document')
                                                ->options(function (Closure $get, Closure $set) {
                                                    if ($get('../../pdf_info') != '') {
                                                        $array = [];
                                                        $json_pdfs = json_decode($get('../../pdf_info'), true);
                                                        foreach ($json_pdfs as $pdf) {
                                                            if ($pdf['name']) {
                                                                $array[] = $pdf['name'];
                                                            }
                                                        }

                                                        return $array;
                                                    }
                                                })->reactive(),
                                        ]),
                                        Placeholder::make('Info')
                                        ->content(new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">Please put this code {basmti} in the position that will be modified by the user</h1>')),
                                        Textarea::make('predefined_text')->rows(5)->extraAttributes(['dir' => 'rtl'])->reactive(),
                                        Grid::make(2)
                                        ->schema([
                                            TextInput::make('X_coord')->default(0)->numeric()->reactive()->minValue(1)->required(),
                                            TextInput::make('Y_coord')->default(0)->numeric()->reactive()->required(),
                                            ColorPicker::make('color')->default('#000000')->reactive()->required(),
                                            TextInput::make('max_width')->default(100)->suffix('PX')->reactive()->numeric()->required(),
                                            TextInput::make('font_size')->default(16)->reactive()->numeric()->required(),
                                            Select::make('font_face')
                                                                                            ->options([
                                                                                                'sans-serif' => 'sans-serif',
                                                                                                'helvetica' => 'Helvetica',
                                                                                            ])->default('helvetica')->reactive()->required(),
                                            Select::make('text_align')
                                                                                            ->options([
                                                                                                'C' => 'Center',
                                                                                                'L' => 'Left',
                                                                                                'R' => 'Right',
                                                                                            ])->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                                                                                                if ($get('document') != '' && $get('../../pdf_info') != '') {
                                                                                                    $json_pdfs = json_decode($get('../../pdf_info'), true);
                                                                                                    $key = array_search($get('document'), array_column($json_pdfs, 'filename'));
                                                                                                    $json_pdfs[$key]['type'] = $state;
                                                                                                    $img_page = (int) $get('page');
                                                                                                    //$set('image', [$get('predefined_text'), asset($json_pdfs[0]['pdf'][$img_page]), $get('text_align')]);
                                                                                                }
                                                                                            })->default('R')->reactive()->required(),

                                        ]),
                                        PDFPositioner::make('image')->set_pdf_data(function (Closure $get, $state) {
                                            if ($get('page') != '') {
                                                $json_pdfs = json_decode($get('../../pdf_info'), true);
                                                $key = array_search($get('document'), array_column($json_pdfs, 'filename'));
                                                $json_pdfs[$key]['type'] = $get('page');
                                                $img_page = (int) $get('page');

                                                return [
                                                    'predefined_text' => $get('predefined_text'),
                                                    'page' => asset($json_pdfs[0]['pdf'][$img_page]),
                                                    'text_align' => $get('text_align'),
                                                    'X' => $get('X_coord'),
                                                    'Y' => $get('Y_coord'),
                                                    'width' => $get('max_width'),
                                                    'font_size' => $get('font_size'),
                                                    'color' => $get('color'),
                                                ];
                                            }

                                            return [];
                                        }
                                        )
                                        ->reactive(),
                                    ])->collapsible(),
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
