<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Actions\HandleDocType;
use App\Filament\Resources\Actions\HandleDocumentName;
use App\Filament\Resources\Actions\HandleDocumentOptions;
use App\Filament\Resources\Actions\HandleEditorPageNumber;
use App\Filament\Resources\Actions\HandlePageBarcodeUpdated;
use App\Filament\Resources\Actions\HandlePageOptions;
use App\Filament\Resources\Actions\HandlePageUpdated;
use App\Filament\Resources\Actions\HandleProductAttatchment;
use App\Filament\Resources\Actions\MakeSlug;
use App\Filament\Resources\Actions\SetPdfDataForDedicationPositioner;
use App\Filament\Resources\Actions\SetPdfDataForPositioner;
use App\Filament\Resources\NewProductResource\Pages;
use App\Forms\Components\CustomRepeater;
use App\Forms\Components\PDFBarcodeEditor;
use App\Forms\Components\PDFEditor;
use App\Models\Product;
use App\Models\Tags;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Unique;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use RalphJSmit\Filament\SEO\SEO;

class NewProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static bool $shouldRegisterNavigation = false;
    //protected static ?string $navigationLabel = 'Product Management';

    protected static ?string $slug = 'new-products';

    public static function form(Form $form): Form
    {
        //session()->remove('documents');

        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Add Product')
                                ->schema([
                                    ViewField::make('fonts')->dehydrated(false)->view('forms.components.fonts-loader'),
                                    //ViewField::make('auto_save')->dehydrated(false)->view('forms.components.auto-save')->hiddenOn('edit'),
                                    Placeholder::make('Info')
                                        ->extraAttributes(['dir' => 'rtl'])
                                        ->content(new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">Please put this code {basmti} in the position that will be modified by the user</h1>')),
                                    //Define the product type as A Personalized Notebook
                                    Hidden::make('product_type')->default(2),
                                    Grid::make(2)->schema([
                                        TextInput::make('demo_name')->placeholder('Enter Product Name With the {basmti}')
                                            ->regex('/basmti/')
                                            ->required()
                                            ->extraAttributes(['dir' => 'rtl']),
                                        TextInput::make('replace_name')->placeholder('Enter A Dummy name to replace {basmti}')
                                            ->string()
                                            ->required()
                                            ->maxLength(255)
                                            ->extraAttributes(['dir' => 'rtl']),
                                    ])->extraAttributes(['dir' => 'rtl']),
                                    TextInput::make('product_name')->placeholder('Enter Product Name')->required()
                                        ->extraAttributes(['dir' => 'rtl'])
                                        ->afterStateUpdated(Closure::fromCallable(new MakeSlug()))->reactive(),
                                    Textarea::make('excerpt')->rows(4)->extraAttributes(['dir' => 'rtl']),
                                    TextInput::make('slug')
                                        ->unique(ignorable: fn ($record) => $record)
                                    //->unique(callback: function (Unique $rule, $state, $context, $record) {
                                        //if ($context != 'edit') {
                                            //return $rule->where('slug', $state)->where('is_published', true);
                                        //}
                                    //})
                                        ->reactive()->placeholder('product-name')
                                        ->extraAttributes(['dir' => 'rtl'])
                                        ->required(),
                                    TextInput::make('price')->placeholder('1.00')->numeric()->minValue(1)->required(),
                                    Toggle::make('is_published')->label('Published')->hiddenOn('create'),
                                    Toggle::make('is_rtl')->label('Is RTL'),
                                    TextInput::make('discount_percentage')->numeric()->minValue(0)->maxValue(99),
                                    Checkbox::make('featured')->label('Featured Product'),
                                    CheckboxList::make('product_attributes')
                                        ->relationship('product_attributes', 'name')->required(),

                                    CheckboxList::make('languages')
                                        ->options(['hebrew' => 'Hebrew', 'drawing' => 'Drawing', 'mathematics' => 'Mathematics', 'arabic' => 'Arabic', 'english' => 'English'])->required(),
                                    Select::make('tags')
                                        ->multiple()
                                        ->extraAttributes(['dir' => 'rtl'])->preload()
                                        ->relationship('tags', 'name')->searchable(),
                                    Select::make('categories')->relationship('categories', 'name')->label('Category')->required()
                                        ->multiple()
                                        ->extraAttributes(['dir' => 'rtl'])
                                        ->preload()
                                        ->searchable(),
                                    //Select::make('tags')->label('Tags')
                                    //->relationship('tags', 'name')->required()
                                    //->extraAttributes(['dir' => 'rtl'])
                                    //->multiple()
                                    //->options(Tags::all()->pluck('name', 'id'))->searchable(),
                                    TinyEditor::make('description')->minHeight(300)->profile('custom')->required(),
                                    FileUpload::make('images')
                                        ->required()
                                        ->enableReordering()
                                        //->disk('do')
                                        ->image()
                                        ->uploadProgressIndicatorPosition('left')
                                        //->directory('storage/uploads')
                                        ->directory('uploads')
                                        ->multiple(),
                                    Placeholder::make('Seo')->content(new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">SEO For Product</h1>')),
                                    SEO::make(),
                                ]),
                            /* Step2 */
                            Wizard\Step::make('Add Documents')
                                ->schema([
                                    Hidden::make('pdf_info'),
                                    Placeholder::make('Upload Your Documents')->content(function (Closure $get, Closure $set, $state) {
                                        //$documents = $get('Documents');
                                        //$file_names = [];
                                        //foreach ($documents as $document) {
                                        //$file_names[] = $document['pdf_name'];
                                        //}
                                        //$pdf_info = json_decode($get('pdf_info'), true);
                                        //if ($pdf_info) {
                                        //$found_key = null;
                                        //$new_array = [];
                                        //$pages = $get('pages');
                                        //$new_pages = [];
                                        //$dedications = $get('dedications');
                                        //$new_dedications = [];
                                        //$barcodes = $get('barcodes');
                                        //$new_barcodes = [];
                                        //foreach ($documents as $document) {
                                        //foreach ($pdf_info as $key => $pdf_in) {
                                        //if (array_key_exists('filename', $pdf_in)) {
                                        //if ($pdf_in['filename'] == $document['pdf_name']) {
                                        //$new_array[] = $pdf_in;
                                        //}
                                        //}
                                        //}
                                        //if (count($documents) != count($pdf_info)) {
                                        //foreach ($pages as $key => $page) {
                                        //if ($page['document'] == $document['name']) {
                                        //$new_pages[] = $page;
                                        //}
                                        //}
                                        //$set('pages', $new_pages);
                                        //}
                                        //if (count($documents) != count($pdf_info)) {
                                        //foreach ($dedications as $key => $dedication) {
                                        //if ($dedication['document'] == $document['name']) {
                                        //$new_dedications[] = $dedication;
                                        //}
                                        //}
                                        //$set('dedications', $new_dedications);
                                        //}

                                        //if (count($documents) != count($pdf_info)) {
                                        //foreach ($barcodes as $key => $barcode) {
                                        //if ($barcode['document'] == $document['name']) {
                                        //$new_barcodes[] = $barcode;
                                        //}
                                        //}
                                        //$set('barcodes', $new_barcodes);
                                        //}
                                        //}
                                        //$set('pdf_info', json_encode($new_array));
                                        //}

                                        //return new HtmlString('');
                                    }),
                                    CustomRepeater::make('Documents')
                                        ->relationship('documents')
                                        ->schema([
                                            Grid::make(4)
                                                ->schema([
                                                    TextInput::make('name')->required()
                                                        ->unique(ignorable: fn ($record) => $record)
                                                     //->unique(callback: function (Unique $rule, $state, $context, $record) {
                                                         //if ($context != 'edit') {
                                                             //if ($record) {
                                                                 //$product_published = $record->product->is_published;
                                                                 //if ($product_published && $rule->where('name', $state)) {
                                                                     //return true;
                                                                 //}
                                                             //}
                                                         //}
                                                     //})
                                                        ->extraAttributes(['dir' => 'rtl'])
                                                        ->helperText('Document name must be unique')
                                                        ->reactive()->afterStateUpdated(Closure::fromCallable(new HandleDocumentName())),
                                                    Radio::make('language')
                                                        ->inlineLabel()
                                                        ->helperText('Choose English if the cover is english only else leave it blank')
                                                        ->options(fn (Closure $get) => $get('type') == '0' ?  ['english'=>'English'] : ['hebrew' => 'Hebrew', 'drawing' => 'Drawing', 'mathematics' => 'Mathematics', 'arabic' => 'Arabic', 'english' => 'English'])
                                                        ->required(fn (Closure $get)=>$get('type') == '0' ? false : true),
                                                    Select::make('type')
                                                        ->options([
                                                            '0' => 'Notebook Cover',
                                                            '2' => 'NoteBook',
                                                        ])->required()->reactive()
                                                        ->helperText('Select a Type To Upload a Document')
                                                        ->disabled(function (Closure $get) {
                                                            if ($get('pdf_name') != null) {
                                                                return true;
                                                            }

                                                            return false;
                                                        })
                                                        ->afterStateUpdated(Closure::fromCallable(new HandleDocType())),
                                                    Hidden::make('pdf_name'),
                                                    Hidden::make('dimensions'),
                                                ])->reactive(),
                                            FileUpload::make('attatchment')
                                                ->acceptedFileTypes(['application/pdf'])
                                                ->directory('uploads')
                                                ->helperText('File must be a pdf and must be smaller than 500mb')->maxSize(500 * 1024) //20MB
                                                ->hidden(fn (Closure $get): bool => is_numeric($get('type')) ? false : true)
                                                ->required()
                                                ->reactive()->enableDownload()
                                                ->afterStateUpdated(Closure::fromCallable(new HandleProductAttatchment())),
                                        ])->minItems(2),
                                ]),
                            /* Step3 */
                            Wizard\Step::make('Add Content')
                                ->schema([
                                    Placeholder::make('Add Content for Your Documents')->reactive(),
                                    Select::make('fonts_update')->dehydrated(false)
                                        ->options(function (Closure $get) {
                                            $fonts = \App\Models\Fonts::all();
                                            $fonts_array = ['GE-Dinar-Medium' => 'GE-Dinar-Medium'];
                                            foreach ($fonts as $font) {
                                                $fonts_array = array_merge($fonts_array, [$font->font_name => $font->font_name]);
                                            }

                                            return $fonts_array;
                                        })->afterStateUpdated(function (Closure $get, Closure $set, $state) {
                                            $pages = $get('pages');
                                            $new_pages = [];
                                            if ($pages) {
                                                foreach ($pages as &$page) {
                                                    if ($page['pages']) {
                                                        if ($page['pages']['predefined_texts']) {
                                                            foreach ($page['pages']['predefined_texts'] as &$text) {
                                                                $text['font_face'] = $state;
                                                            }
                                                        }
                                                    }
                                                }
                                                $set('pages', $pages);
                                            }
                                        })->reactive(),
                                    //->reactive(),
                                    Placeholder::make('Info')
                                        ->content(new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">{basmti} For Fullname</h1>
                                                              <!--<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">{f_name} For Firstname</h1>-->
                                                              <h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">{init} For Initial Letter</h1>
                                                              <h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">{age} For Age</h1>
                                  ')),
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
                                                        ->disabled(function (Closure $set, Closure $get, $state) {
                                                            if ($get('document') != '' && $get('../../pdf_info') != '') {
                                                                return false;
                                                            }

                                                            return false;
                                                        })->reactive(),
                                                ]),
                                            PDFEditor::make('pages')->set_pdf_data(Closure::fromCallable(new SetPdfDataForPositioner()))
                                                ->reactive(),
                                        ])->minItems(1)->collapsible()->itemLabel(Closure::fromCallable(new HandleEditorPageNumber())),
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
                                            PDFBarcodeEditor::make('barcodes')->set_pdf_data(Closure::fromCallable(new SetPdfDataForDedicationPositioner()))
                                                ->reactive(),
                                        ])->minItems(1)->maxItems(2)->collapsible(),
                                ]),
                        ])->skippable(function ($context) {
                            if ($context == 'edit') {
                                return true;
                            }

                            return false;
                            //return false;
                        }),
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
            'index' => \App\Filament\Resources\ProductResource\Pages\ListProducts::route('/'),
            'create' => Pages\CreateNewProduct::route('/create'),
            'edit' => Pages\EditNewProduct::route('/{record}/edit'),
        ];
    }
}
