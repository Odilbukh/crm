<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-lightning-bolt';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'shop/items';

    protected static ?string $navigationLabel = 'Items';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Card::make()->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->lazy()
                            ->afterStateUpdated(
                                fn(string $context, $state, callable $set) => $context === 'create' ? $set(
                                    'slug',
                                    Str::slug($state)
                                ) : null
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->disabled()
                            ->required()
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        Forms\Components\MarkdownEditor::make('description')
                            ->columnSpan('full'),
                    ])
                        ->columns(2),

                    Forms\Components\Section::make('Images')->schema([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->collection('product-images')
                            ->multiple()
                            ->maxFiles(5)
                            ->enableReordering()
                            ->disableLabel(),
                    ])
                        ->collapsible(),

                    Forms\Components\Section::make('Pricing')->schema([
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->mask(fn(Forms\Components\TextInput\Mask $mask) => $mask->money('', ' ', 2)),


                        Forms\Components\TextInput::make('old_price')
                            ->label('Compare at price')
                            ->numeric()
                            ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),

                        Forms\Components\TextInput::make('net_price')
                            ->label('Cost per item')
                            ->helperText('Customers won\'t see this price.')
                            ->numeric()
                            ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                    ])
                        ->columns(2),

                    Forms\Components\Section::make('Inventory')->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU (Stock Keeping Unit)')
                            ->unique(Product::class, 'sku', ignoreRecord: true),

                        Forms\Components\TextInput::make('barcode')
                            ->label('Barcode (ISBN, UPC, GTIN, etc.)')
                            ->unique(Product::class, 'barcode', ignoreRecord: true),

                        Forms\Components\TextInput::make('qty')
                            ->label('Quantity')
                            ->numeric()
                            ->rules(['integer', 'min:0'])
                            ->default(0),

                        Forms\Components\TextInput::make('security_stock')
                            ->helperText(
                                'The safety stock is the limit stock for your products which alerts you if the product stock will soon be out of stock.'
                            )
                            ->numeric()
                            ->rules(['integer', 'min:0'])
                            ->default(0),

                        Forms\Components\TextInput::make('weight_value')
                            ->label('Weight value')
                            ->numeric()
                            ->rules(['integer', 'min:0']),

                        Forms\Components\TextInput::make('weight_unit')
                            ->label('Weight unit')
                            ->default('kg'),

                        Forms\Components\TextInput::make('height_value')
                            ->label('height value')
                            ->numeric()
                            ->rules(['integer', 'min:0']),

                        Forms\Components\TextInput::make('height_unit')
                            ->label('Height unit')
                            ->default('cm'),

                        Forms\Components\TextInput::make('width_value')
                            ->label('Width value')
                            ->numeric()
                            ->rules(['integer', 'min:0']),

                        Forms\Components\TextInput::make('width_unit')
                            ->label('width_unit')
                            ->default('cm'),

                        Forms\Components\TextInput::make('depth_value')
                            ->label('Depth value')
                            ->numeric()
                            ->rules(['integer', 'min:0']),

                        Forms\Components\TextInput::make('depth_unit')
                            ->label('Depth unit')
                            ->default('cm'),

                        Forms\Components\TextInput::make('volume_value')
                            ->label('Volume value')
                            ->numeric()
                            ->rules(['integer', 'min:0']),

                        Forms\Components\TextInput::make('volume_unit')
                            ->label('Volume unit')
                            ->default('l')
                    ])
                        ->collapsed()
                        ->columns(2),
                ])
                    ->columnSpan(['lg' => 2]),


                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Status')->schema([
                        Forms\Components\Toggle::make('is_visible')
                            ->label('Visible')
                            ->helperText('This product will be hidden from all sales channels.')
                            ->default(true),

                        Forms\Components\DatePicker::make('published_at')
                            ->label('Availability')
                            ->default(now())
                            ->required(),
                    ]),

                    Forms\Components\Section::make('Associations')->schema([
                        Forms\Components\Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->hiddenOn(ProductsRelationManager::class)
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),

                                Forms\Components\TextInput::make('website')
                                    ->required(),
                            ])
                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                return $action
                                    ->modalHeading('Create brand')
                                    ->modalButton('Create brand')
                                    ->modalWidth('lg');
                            }),

                        Forms\Components\Select::make('categories')
                            ->multiple()
                            ->relationship('categories', 'name')
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),

                                Forms\Components\Select::make('parent_id')
                                    ->label('Parent')
                                    ->relationship(
                                        'parent',
                                        'name',
                                        fn(Builder $query) => $query->where('parent_id', null)
                                    )
                                    ->searchable()
                                    ->placeholder('Select parent category'),
                            ])
                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                return $action
                                    ->modalHeading('Create category')
                                    ->modalButton('Create category')
                                    ->modalWidth('lg');
                            }),
                    ]),

                    Forms\Components\Section::make('Shipping')->schema([
                        Forms\Components\Checkbox::make('backorder')
                            ->label('This product can be returned'),

                        Forms\Components\Checkbox::make('requires_shipping')
                            ->label('This product will be shipped'),
                    ])
                ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('product-image')
                    ->label('Image')
                    ->collection('product-images'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('qty')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('security_stock')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean()
                    ->label('Visibility')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publish Date')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->action(function () {
                        Notification::make()
                            ->title('Now, now, don\'t be cheeky, leave some records for others to play with!')
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\CommentsRelationManager::class,
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

//    public static function getWidgets(): array
//    {
//        return [
//            ProductStats::class,
//        ];
//    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku', 'brand.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Product $record */

        return [
            'Brand' => optional($record->brand)->name,
        ];
    }

    protected static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['brand']);
    }

    protected static function getNavigationBadge(): ?string
    {
        return self::$model::count();
    }
}
