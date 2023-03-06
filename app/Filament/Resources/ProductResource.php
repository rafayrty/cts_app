<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Actions\HandleDocType;
use App\Filament\Resources\Actions\HandleDocumentName;
use App\Filament\Resources\Actions\HandleDocumentOptions;
use App\Filament\Resources\Actions\HandlePageBarcodeUpdated;
use App\Filament\Resources\Actions\HandlePageDedicationUpdated;
use App\Filament\Resources\Actions\HandlePageOptions;
use App\Filament\Resources\Actions\HandlePageUpdated;
use App\Filament\Resources\Actions\HandleProductAttatchment;
use App\Filament\Resources\Actions\MakeSlug;
use App\Filament\Resources\Actions\SetPdfDataForDedicationPositioner;
use App\Filament\Resources\Actions\SetPdfDataForPositioner;
use App\Filament\Resources\ProductResource\Pages;
use App\Forms\Components\PDFBarcodeEditor;
use App\Forms\Components\PDFDedicationEditor;
use App\Forms\Components\PDFEditor;
use App\Models\Category;
use App\Models\Product;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
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
use RalphJSmit\Filament\SEO\SEO;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Product Management';

    protected static $pdfs = [];

    public static function form(Form $form): Form
    {
        $pdfs = self::$pdfs;

        return $form
            ->schema([
                Card::make()
                ->schema([
                    Wizard::make([
                        Wizard\Step::make('Add Product')
                        ->schema([
                            SEO::make(),
                            ViewField::make('fonts')->dehydrated(false)->view('forms.components.fonts-loader'),
                            Placeholder::make('Info')
                            ->extraAttributes(['dir' => 'rtl'])
                            ->content(new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">Please put this code {basmti} in the position that will be modified by the user</h1>')),
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
                            ->reactive()->placeholder('product-name')
                            ->extraAttributes(['dir' => 'rtl'])
                            ->required(),
                            TextInput::make('price')->placeholder('1.00')->numeric()->minValue(1)->required(),
                            TextInput::make('discount_percentage')->numeric()->minValue(0)->maxValue(99),
                            Checkbox::make('featured')->label('Featured Product'),
                            CheckboxList::make('product_attributes')
                                ->relationship('product_attributes', 'name')->required(),
                            CheckboxList::make('covers')
                                ->relationship('covers', 'name')->required(),
                            Select::make('category_id')->label('Category')->required()
                            ->extraAttributes(['dir' => 'rtl'])
                            ->options(Category::all()->pluck('name', 'id'))->searchable(),
                            TinyEditor::make('description')->minHeight(300)->profile('custom')->required(),
                            FileUpload::make('images')
                                ->required()
                                ->enableReordering()
                                ->image()
                                ->uploadProgressIndicatorPosition('left')
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
                                     Grid::make(3)
                                     ->schema([
                                         TextInput::make('name')->required()
                                          ->unique(ignorable: fn ($record) => $record)
                                          ->extraAttributes(['dir' => 'rtl'])
                                          ->helperText('Document name must be unique')
                                          ->reactive()->afterStateUpdated(Closure::fromCallable(new HandleDocumentName())),
                                         CheckboxList::make('gender')
                                            ->inlineLabel()
                                            ->options(['Male' => 'Male', 'Female' => 'Female'])->required(),
                                         Select::make('type')
                                             ->options([
                                                 '1' => 'Cover',
                                                 '2' => 'Book',
                                             ])->required()->reactive()
                                              ->helperText('Select a Type To Upload a Document')
                                              ->afterStateUpdated(Closure::fromCallable(new HandleDocType())),
                                         Hidden::make('pdf_name'),
                                         Hidden::make('dimensions'),
                                     ])->reactive(),
                                     FileUpload::make('attatchment')
                                         ->acceptedFileTypes(['application/pdf'])
                                         ->directory('uploads')
                                         ->helperText('File must be a pdf and must be smaller than 500mb')->maxSize(500 * 1024) //20MB
                                         ->hidden(fn (Closure $get): bool => $get('type') != null ? false : true)
                                         ->required()
                                         ->reactive()
                                         ->afterStateUpdated(Closure::fromCallable(new HandleProductAttatchment())),
                                 ])->minItems(1),
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
                                              PDFEditor::make('pages')->set_pdf_data(Closure::fromCallable(new SetPdfDataForPositioner()))
                                              ->reactive(),
                                          ])->minItems(1)->collapsible()->itemLabel(function (array $state) {
                                              if ($state['page'] && $state['document']) {
                                                  return 'Page #'.($state['page'] + 1).' Of '.$state['document'];
                                              }
                                          }),
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
                                              PDFDedicationEditor::make('dedications')->set_pdf_data(Closure::fromCallable(new SetPdfDataForDedicationPositioner()))
                                              ->reactive(),
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
                                             PDFBarcodeEditor::make('barcodes')->set_pdf_data(Closure::fromCallable(new SetPdfDataForDedicationPositioner()))
                                             ->reactive(),
                                         ])->minItems(1)->collapsible(),
                                ]),
                    ]),
                ]),
            ]);
    }

  public static function getNameFormField()
  {
      return TextInput::make('name')
          ->required()
          ->reactive();
  }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_name'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('product_attributes.name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                //Action::make('generate_cover')
                //->label('Cover')
                //->url(fn (Product $record): string => route('preview.pdf', $record->id))
                //->icon('heroicon-o-check-circle')
                //->openUrlInNewTab(),
                Action::make('generate_pdf')
                ->url(fn (Product $record): string => ProductResource::getUrl('generate_pdf', ['id' => $record->id])),
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
            //'generate_pdf' => Pages\GeneratePDFPage::route('/generate_pdf'),
            'generate_pdf' => Pages\GeneratePDFPage::route('/generate_pdf/{id}'),
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
