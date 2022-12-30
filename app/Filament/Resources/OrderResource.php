<?php

namespace App\Filament\Resources;

use App\Models\Product;
use Squire\Models\Currency;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Forms\AddressForm;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\Card::make([
                        Forms\Components\TextInput::make('number')
                            ->default('OR-' . random_int(100000, 999999))
                            ->disabled()
                            ->required(),

                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),

                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(),

                                Forms\Components\TextInput::make('phone')
                                    ->required()
                            ])
                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                return $action
                                    ->modalHeading('Create customer')
                                    ->modalButton('Create customer')
                                    ->modalWidth('lg');
                            }),
                        Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'New',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('new')
                            ->required(),
                        Forms\Components\Select::make('currency')
                            ->searchable()
                            ->getSearchResultsUsing(
                                fn(string $query) => Currency::where('name', 'like', "%{$query}%")->pluck('name', 'id')
                            )
                            ->getOptionLabelUsing(fn($value): ?string => Currency::find($value)?->getAttribute('name'))
                            ->required(),
                        Forms\Components\Select::make('shipping_method')
                            ->options([
                                'currier' => 'Currier',
                                'self-pickup' => 'Self-pickup',
                            ])
                            ->default('currier')
                            ->required(),

                        Forms\Components\TextInput::make('shipping_price')
                            ->mask(fn(Forms\Components\TextInput\Mask $mask) => $mask->money('', ' ', 2)),


                        Forms\Components\MarkdownEditor::make('notes')
                            ->columnSpan('full'),
                    ])->columns([
                        'sm' => 2,
                    ]),

                    Forms\Components\Card::make([
//                        Forms\Components\Placeholder::make('Products'),
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::query()->pluck('name', 'id'))
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(
                                        fn($state, callable $set) => $set(
                                            'unit_price',
                                            Product::find($state)?->price ?? 0
                                        )
                                    )
                                    ->columnSpan([
                                        'md' => 5,
                                    ]),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->disabled()
                                    ->numeric()
                                    ->required()
                                    ->columnSpan([
                                        'md' => 3,
                                    ]),

                                Forms\Components\TextInput::make('qty')
                                    ->numeric()
                                    ->mask(
                                        fn(Forms\Components\TextInput\Mask $mask) => $mask
                                            ->numeric()
                                            ->integer()
                                    )
                                    ->default(1)
                                    ->columnSpan([
                                        'md' => 2,
                                    ])
                                    ->required(),

                            ])
                            ->orderable()
                            ->defaultItems(1)
                            ->disableLabel()
                            ->columns([
                                'md' => 10,
                            ])
                            ->required(),
                    ])
                ])->columnSpan([
                    'sm' => 2,
                ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\TextInput::make('total_price')
                                    ->label('Total Price')
                                    ->disabled()
                                    ->numeric()
                                    ->mask(fn(Forms\Components\TextInput\Mask $mask) => $mask->money('', ' ', 2))
                                    ->required()
                                    ->columnSpan([
                                        'md' => 3,
                                    ]),

                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created at')
                                    ->content(
                                        fn(?Order $record): string => $record ? $record->created_at->diffForHumans(
                                        ) : '-'
                                    )
                                    ->hidden(fn(?Order $record) => $record === null),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Last modified at')
                                    ->content(
                                        fn(?Order $record): string => $record ? $record->updated_at->diffForHumans(
                                        ) : '-'
                                    )
                                    ->hidden(fn(?Order $record) => $record === null),
                            ])
                            ->columnSpan(1),

                        Forms\Components\Card::make()
                            ->schema([
                                AddressForm::make('address')
                                    ->columnSpan('full'),
                            ])
                            ->columnSpan(1),
                    ]),
            ])
            ->columns([
                'sm' => 3,
                'lg' => null,
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
