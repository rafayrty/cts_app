<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Actions\HandleDocType;
use App\Filament\Resources\Actions\HandleDocumentName;
use App\Filament\Resources\Actions\HandleDocumentOptions;
use App\Filament\Resources\Actions\HandlePageBarcodeUpdated;
use App\Filament\Resources\Actions\HandlePageDedicationUpdated;
use App\Filament\Resources\Actions\HandlePageOptions;
use App\Filament\Resources\Actions\HandlePageUpdated;
use App\Filament\Resources\Actions\HandlePdfBarcodePositionerUpdate;
use App\Filament\Resources\Actions\HandlePdfDedicationPositionerUpdate;
use App\Filament\Resources\Actions\HandlePdfPositionerUpdate;
use App\Filament\Resources\Actions\HandleProductAttatchment;
use App\Filament\Resources\Actions\MakeSlug;
use App\Filament\Resources\Actions\SetPdfDataForBarcodePositioner;
use App\Filament\Resources\Actions\SetPdfDataForDedicationPositioner;
use App\Filament\Resources\Actions\SetPdfDataForPositioner;
use App\Filament\Resources\Actions\SetPdfFonts;
use App\Filament\Resources\ProductResource\Pages;
use App\Forms\Components\PdfBarcodePositioner;
use App\Forms\Components\PdfDedicationPositioner;
use App\Forms\Components\PDFPositioner;
use App\Models\Category;
use App\Models\Dedication;
use App\Models\Product;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\CheckboxList;
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
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;

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
                        ViewField::make('fonts')->dehydrated(false)->view('forms.components.fonts-loader'),
                        Placeholder::make('Info')
                        ->extraAttributes(['dir' => 'rtl'])
                        ->content(new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">Please put this code {basmti} in the position that will be modified by the user</h1>')),
                        TextInput::make('demo_name')->placeholder('Enter Product Name With the {name}')->required()
                        ->extraAttributes(['dir' => 'rtl']),
                        TextInput::make('name')->placeholder('Enter Product Name')->required()
                        ->extraAttributes(['dir' => 'rtl'])
                            ->afterStateUpdated(Closure::fromCallable(new MakeSlug()))->reactive(),
                        TextInput::make('slug')->reactive()->placeholder('product-name')
                        ->extraAttributes(['dir' => 'rtl'])
                        ->required(),
                        TextInput::make('price')->placeholder('1.00')->numeric()->minValue(1)->required(),
                        CheckboxList::make('product_attributes')
                            ->relationship('product_attributes', 'name')->required(),
                        Select::make('category_id')->label('Category')
                        ->extraAttributes(['dir' => 'rtl'])
                        ->options(Category::all()->pluck('name', 'id'))->searchable(),
                        RichEditor::make('description')->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ])
                        ->extraAttributes(['dir' => 'rtl'])
                        ->required(),
                        FileUpload::make('images')
                            ->image()
                            ->directory('uploads')
                            ->multiple(),
                    ]),
                    /* Step2 */
                    Wizard\Step::make('Add Documents')
                     ->schema([
                         Hidden::make('pdf_info'),
                         Repeater::make('Documents')
                         ->relationship('documents')
                             ->schema([
                                 Grid::make(2)
                                 ->schema([
                                     TextInput::make('name')->required()
                                     ->extraAttributes(['dir' => 'rtl'])
                                     ->reactive()->afterStateUpdated(Closure::fromCallable(new HandleDocumentName())),
                                     Select::make('type')
                                         ->options([
                                             '1' => 'Cover',
                                             '2' => 'Book',
                                         ])->required()->reactive()
                                          ->afterStateUpdated(Closure::fromCallable(new HandleDocType())),
                                 ]),
                                 FileUpload::make('attatchment')
                                     ->acceptedFileTypes(['application/pdf'])
                                     ->directory('uploads')
                                     ->helperText('File must be a pdf and must be smaller than 500mb')->maxSize(500 * 1024) //20MB
                                     ->required()
                                     ->reactive()
                                     ->afterStateUpdated(Closure::fromCallable(new HandleProductAttatchment())),
                             ])->minItems(1)->maxItems(2),
                     ]),
                    /* Step3 */
                    Wizard\Step::make('Add Content')
                            ->schema([
                                Placeholder::make('Add Content for Your Documents')->reactive(),
                                Repeater::make('pages')
                                     ->schema([
                                         Grid::make(2)
                                         ->schema([
                                             Select::make('page')
                                                 ->options(Closure::fromCallable(new HandlePageOptions()))
                                                 ->disabled(function (Closure $set, Closure $get, $state) {
                                                     if ($get('document') != '' && $get('../../pdf_info') != '') {
                                                         return false;
                                                     }

                                                     return true;
                                                 })->afterStateUpdated(Closure::fromCallable(new HandlePageUpdated()))->reactive()->required(),
                                             Select::make('document')
                                                 ->options(Closure::fromCallable(new HandleDocumentOptions()))
                                                 ->reactive(),
                                         ]),
                                         Placeholder::make('Info')->content(function (Closure $get, Closure $set, $state) {
                                             if ($get('document') != '' && $get('../../pdf_info') != '') {
                                                 $json_pdfs = json_decode($get('../../pdf_info'), true);
                                                 $search_key = search_key($json_pdfs, $get('document'));
                                                 $json_pdfs[$search_key]['type'] = $state;
                                                 $img_page = (int) $get('page');

                                                 $repeater_fields = $get('predefined_texts');
                                                 $new_fields = [];
                                                 foreach ($repeater_fields as $key => $field) {
                                                     array_push($new_fields, ['field_key' => $key, 'value' => $field]);
                                                 }
                                                 $set('image',
                                                     [
                                                         'predefined_texts' => $new_fields,
                                                         'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                                                     ]);
                                             }

                                             return new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">Please put this code {basmti} in the position that will be modified by the user</h1>');
                                         }),
                                         //Textarea::make('predefined_text')->rows(5)->extraAttributes(['dir' => 'rtl'])->reactive(),
                                         Repeater::make('predefined_texts')
                                           ->schema([
                                               Grid::make(9)
                                               ->schema([
                                                   Hidden::make('text')->default('Enter Text')->required(),
                                                   Hidden::make('width_percent')->default('')->reactive(),
                                                   Hidden::make('X_coord_percent')->default(0)->reactive(),
                                                   Hidden::make('Y_coord_percent')->default(0)->reactive(),
                                                   TextInput::make('X_coord')->default(0)->numeric()->reactive()->required(),
                                                   TextInput::make('Y_coord')->default(0)->numeric()->reactive()->required(),
                                                   ColorPicker::make('color')->rgb()->default('rgb(0,0,0)')->reactive()->required(),
                                                   ColorPicker::make('bg_color')->default('rgba(0,0,0,0)')
                                                          ->rgba()->reactive()->required(),
                                                   TextInput::make('max_width')->default(100)
                                                   ->minValue(100)->suffix('PX')->reactive()->numeric()->required(),
                                                   TextInput::make('font_size')->default(16)->reactive()->numeric()->required(),
                                                   Select::make('font_face')
                                                   ->options(Closure::fromCallable(new SetPdfFonts()))->default('NotoSansArabic-Regular')->searchable()->reactive()->required()->columnSpan(2),
                                                   Select::make('text_align')
                                                                   ->options([
                                                                       'C' => 'Center',
                                                                       'L' => 'Left',
                                                                       'R' => 'Right',
                                                                   ])
                                                         ->default('R')->reactive()->required(),
                                               ]),
                                           ])->minItems(1)->reactive(),
                                         PDFPositioner::make('image')->set_pdf_data(Closure::fromCallable(new SetPdfDataForPositioner()))
                                         ->reactive()->afterStateUpdated(Closure::fromCallable(new HandlePdfPositionerUpdate())),
                                     ])->minItems(1)->collapsible(),
                            ]),
                    Wizard\Step::make('Position Dedications')
                            ->schema([
                                Repeater::make('dedications')
                                     ->schema([
                                         Grid::make(3)
                                         ->schema([
                                             Select::make('page')
                                                 ->options(Closure::fromCallable(new HandlePageOptions()))
                                                 ->disabled(function (Closure $set, Closure $get, $state) {
                                                     if ($get('document') != '' && $get('../../pdf_info') != '') {
                                                         return false;
                                                     }

                                                     return true;
                                                 })->afterStateUpdated(Closure::fromCallable(new HandlePageDedicationUpdated()))->reactive()->required(),
                                             Select::make('document')
                                                 ->options(Closure::fromCallable(new HandleDocumentOptions()))->reactive(),
                                         ]),
                                         Placeholder::make('Info')->content(function (Closure $get, Closure $set, $state) {
                                             if ($get('document') != '' && $get('../../pdf_info') != '') {
                                                 $json_pdfs = json_decode($get('../../pdf_info'), true);
                                                 $search_key = search_key($json_pdfs, $get('document'));
                                                 $json_pdfs[$search_key]['type'] = $state;
                                                 $img_page = (int) $get('page');

                                                 $repeater_fields = $get('dedication_texts');
                                                 $new_fields = [];
                                                 foreach ($repeater_fields as $key => $field) {
                                                     array_push($new_fields, ['field_key' => $key, 'value' => $field]);
                                                 }
                                                 $set('image',
                                                     [
                                                         'dedication_texts' => $new_fields,
                                                         'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                                                     ]);
                                             }

                                             return new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">Please put this code {basmti} in the position that will be modified by the user</h1>');
                                         }),
                                         Hidden::make('dedication_text')->reactive(),
                                         //Textarea::make('predefined_text')->rows(5)->extraAttributes(['dir' => 'rtl'])->reactive(),
                                         Repeater::make('dedication_texts')
                                           ->schema([
                                               Grid::make(9)
                                               ->schema([
                                                   //Hidden::make('text')->default(Dedication::all()->first()->dedication ?? "Enter a dedication")->reactive(),
                                                   Hidden::make('text')->default(Dedication::all()->first()->dedication ?? 'Enter a dedication')->reactive(),
                                                   Hidden::make('width_percent')->default('')->reactive(),
                                                   Hidden::make('X_coord_percent')->default(0)->reactive(),
                                                   Hidden::make('Y_coord_percent')->default(0)->reactive(),
                                                   TextInput::make('X_coord')->default(0)->numeric()->reactive()->required(),
                                                   TextInput::make('Y_coord')->default(0)->numeric()->reactive()->required(),
                                                   ColorPicker::make('color')->rgb()->default('rgb(0,0,0)')->reactive()->required(),
                                                   ColorPicker::make('bg_color')->default('rgba(0,0,0,0)')
                                                          ->rgba()->reactive()->required(),
                                                   TextInput::make('max_width')->default(100)
                                                   ->minValue(100)->suffix('PX')->reactive()->numeric()->required(),
                                                   TextInput::make('font_size')->default(16)->reactive()->numeric()->required(),
                                                   Select::make('font_face')
                                                   ->options([
                                                       'sans-serif' => 'sans-serif',
                                                       'helvetica' => 'Helvetica',
                                                   ])->default('helvetica')->searchable()->reactive()->required()->columnSpan(2),
                                                   Select::make('text_align')
                                                                   ->options([
                                                                       'C' => 'Center',
                                                                       'L' => 'Left',
                                                                       'R' => 'Right',
                                                                   ])
                                                         ->default('R')->reactive()->required(),
                                               ]),
                                           ])->minItems(1)->reactive(),
                                         PdfDedicationPositioner::make('image')->set_pdf_data(Closure::fromCallable(new SetPdfDataForDedicationPositioner()))
                                         ->reactive()->afterStateUpdated(Closure::fromCallable(new HandlePdfDedicationPositionerUpdate())),
                                     ])->minItems(1)->collapsible(),
                            ]),

                    Wizard\Step::make('Position Barcodes')
                            ->schema([
                                Repeater::make('barcodes')
                                     ->schema([
                                         Grid::make(3)
                                         ->schema([
                                             Select::make('page')
                                                 ->options(Closure::fromCallable(new HandlePageOptions()))
                                                 ->disabled(function (Closure $set, Closure $get, $state) {
                                                     if ($get('document') != '' && $get('../../pdf_info') != '') {
                                                         return false;
                                                     }

                                                     return true;
                                                 })->afterStateUpdated(Closure::fromCallable(new HandlePageBarcodeUpdated()))->reactive()->required(),
                                             Select::make('document')
                                                 ->options(Closure::fromCallable(new HandleDocumentOptions()))->reactive(),
                                         ]),
                                         Placeholder::make('Info')->content(function (Closure $get, Closure $set, $state) {
                                             if ($get('document') != '' && $get('../../pdf_info') != '') {
                                                 $json_pdfs = json_decode($get('../../pdf_info'), true);
                                                 $search_key = search_key($json_pdfs, $get('document'));
                                                 $json_pdfs[$search_key]['type'] = $state;
                                                 $img_page = (int) $get('page');

                                                 $repeater_fields = $get('barcode_info');
                                                 $new_fields = [];
                                                 foreach ($repeater_fields as $key => $field) {
                                                     array_push($new_fields, ['field_key' => $key, 'value' => $field]);
                                                 }
                                                 $set('image',
                                                     [
                                                         'barcode_info' => $new_fields,
                                                         'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                                                     ]);
                                             }

                                             return new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">Please put this code {basmti} in the position that will be modified by the user</h1>');
                                         }),
                                         //Textarea::make('predefined_text')->rows(5)->extraAttributes(['dir' => 'rtl'])->reactive(),
                                         Repeater::make('barcode_info')
                                           ->schema([
                                               Grid::make(9)
                                               ->schema([
                                                   //Hidden::make('text')->default(Dedication::all()->first()->dedication ?? "Enter a dedication")->reactive(),
                                                   Hidden::make('width_percent')->default('')->reactive(),
                                                   Hidden::make('X_coord_percent')->default(0)->reactive(),
                                                   Hidden::make('Y_coord_percent')->default(0)->reactive(),
                                                   TextInput::make('X_coord')->default(0)->numeric()->reactive()->required(),
                                                   TextInput::make('Y_coord')->default(0)->numeric()->reactive()->required(),
                                                   TextInput::make('max_width')->default(100)
                                                   ->minValue(100)->suffix('PX')->reactive()->numeric()->required(),
                                               ]),
                                           ])->minItems(1)->reactive(),
                                         PdfBarcodePositioner::make('image')->set_pdf_data(Closure::fromCallable(new SetPdfDataForBarcodePositioner()))
                                         ->reactive()->afterStateUpdated(Closure::fromCallable(new HandlePdfBarcodePositionerUpdate())),
                                     ])->minItems(1)->collapsible(),
                            ]),
                    //Wizard\Step::make('Barcode & Dedication')
                    //->schema([
                    //Placeholder::make('Add Dedications for Your Documents')->reactive(),
                    //Repeater::make('dedications')
                    //->schema([
                    //Grid::make(3)
                    //->schema([
                    //Select::make('page')
                    //->options(Closure::fromCallable(new HandlePageOptions()))
                    //->disabled(function (Closure $set, Closure $get, $state) {
                    //if ($get('document') != '' && $get('../../pdf_info') != '') {
                    //return false;
                    //}
                    //return true;
                    //})->afterStateUpdated(Closure::fromCallable(new HandlePageUpdated()))->reactive()->required(),
                    //Select::make('dedications')
                    //->options(Closure::fromCallable(new HandleDocumentOptions()))->searchable()->reactive(),
                    //Select::make('document')
                    //->options(Closure::fromCallable(new HandleDocumentOptions()))->reactive(),
                    //]),
                    //Placeholder::make('Info')->content(function (Closure $get, Closure $set, $state) {
                    //if ($get('document') != '' && $get('../../pdf_info') != '') {
                    //$json_pdfs = json_decode($get('../../pdf_info'), true);
                    //$key = array_search($get('document'), array_column($json_pdfs, 'filename'));
                    //$json_pdfs[$key]['type'] = $state;
                    //$img_page = (int) $get('page');

                    //$repeater_fields = $get('predefined_texts');
                    //$new_fields = [];
                    //foreach ($repeater_fields as $key => $field) {
                    //array_push($new_fields, ['field_key' => $key, 'value' => $field]);
                    //}
                    //$set('image',
                    //[
                    //'predefined_texts' => $new_fields,
                    //'page' => asset($json_pdfs[0]['pdf'][$img_page]),
                    //]);
                    //}

                    //return new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">Please put this code {basmti} in the position that will be modified by the user</h1>');
                    //}),
                    ////Textarea::make('predefined_text')->rows(5)->extraAttributes(['dir' => 'rtl'])->reactive(),
                    //Repeater::make('dedications_text')
                    //->schema([
                    //Grid::make(9)
                    //->schema([
                    //Hidden::make('text')->default('Enter Text')->required(),
                    //Hidden::make('width_percent')->default('')->reactive(),
                    //Hidden::make('X_coord_percent')->default(0)->reactive(),
                    //Hidden::make('Y_coord_percent')->default(0)->reactive(),
                    //TextInput::make('X_coord')->default(0)->numeric()->reactive()->required(),
                    //TextInput::make('Y_coord')->default(0)->numeric()->reactive()->required(),
                    //ColorPicker::make('color')->rgb()->default('rgb(0,0,0)')->reactive()->required(),
                    //ColorPicker::make('bg_color')->default('rgba(0,0,0,0)')
                    //->rgba()->reactive()->required(),
                    //TextInput::make('max_width')->default(100)
                    //->minValue(100)->suffix('PX')->reactive()->numeric()->required(),
                    //TextInput::make('font_size')->default(16)->reactive()->numeric()->required(),
                    //Select::make('font_face')
                    //->options([
                    //'sans-serif' => 'sans-serif',
                    //'helvetica' => 'Helvetica',
                    //])->default('helvetica')->searchable()->reactive()->required()->columnSpan(2),
                    //Select::make('text_align')
                    //->options([
                    //'C' => 'Center',
                    //'L' => 'Left',
                    //'R' => 'Right',
                    //])
                    //->default('R')->reactive()->required(),
                    //]),
                    //])->minItems(1)->reactive(),
                    //PdfDedicationPositioner::make('image')->set_pdf_data(Closure::fromCallable(new SetPdfDataForPositioner()))
                    //->reactive()->afterStateUpdated(Closure::fromCallable(new HandlePdfPositionerUpdate())),
                    //])->minItems(1)->collapsible(),
                    //]),
                ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('generate_cover')
                    ->label('Cover')
                    ->url(fn (Product $record): string => route('preview.pdf', $record->id))
                    ->icon('heroicon-o-check-circle')
                    ->openUrlInNewTab(),
                Action::make('generate_pdf')
                    ->label('Book')
                    ->url(fn (Product $record): string => route('preview.pdf', $record->id))
                    ->icon('heroicon-o-check-circle')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
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

    public function searchkey($array, $search)
    {
        $key = null;
        foreach ($array as $key => $value) {
            if ($value['name'] == $search) {
                return $key;
            }
        }
    }
}
