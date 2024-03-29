<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Forms\AddressForm;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tax;
use App\Settings\GeneralSettings;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Squire\Models\Currency;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?string $slug = 'shop/orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Card::make()->schema([
                        Forms\Components\TextInput::make('number')
                            ->default('OR-' . random_int(100000, 999999))
                            ->disabled()
                            ->required(),

                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->options(Customer::query()->pluck('name', 'id'))
                            ->label('Customer')
                            ->searchable()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),

                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(),

                                Forms\Components\TextInput::make('phone_1')
                                    ->label('Phone number')
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
                        Forms\Components\Select::make('taxes')
                            ->relationship('taxes', 'name')
                            ->options(Tax::query()->pluck('name', 'id'))
                            ->searchable()
                            ->multiple()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),

                                Forms\Components\TextInput::make('rate')
                                    ->required(),

                                Forms\Components\Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'fixed' => 'Fixed',
                                        'percentage' => 'Percentage',
                                        'compound' => 'Compound',
                                        'withholding' => 'Withholding'
                                    ])
                                    ->default('fixed')
                                    ->required(),
                            ])
                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                return $action
                                    ->modalHeading('Create tax')
                                    ->modalButton('Create tax')
                                    ->modalWidth('lg');
                            }),
                        Forms\Components\Select::make('shipping_method')
                            ->options([
                                'currier' => 'Currier',
                                'self-pickup' => 'Self-pickup',
                            ])
                            ->default('currier')
                            ->required(),

                        Forms\Components\TextInput::make('shipping_price')
                            ->numeric()
                            ->default(0)
                            ->mask(fn(Forms\Components\TextInput\Mask $mask) => $mask->money('', ' ', 2)),

                        Forms\Components\MarkdownEditor::make('notes')
                            ->columnSpanFull(),

                    ])->columns([
                        'sm' => 2,
                    ]),

                ])->columnSpan([
                    'sm' => 2,
                ]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Card::make()->schema([
                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Price')
                            ->helperText(
                                'Total price will be calculated when you click to save. It includes shipping price, taxes and items cost.'
                            )
                            ->disabled()
                            ->default(0)
                            ->columnSpan([
                                'md' => 3,
                            ]),
                        Forms\Components\TextInput::make('currency')
                            ->default(
                                fn(GeneralSettings $settings): string => Currency::find($settings->site_currency)?->name
                            )
                            ->disabled(),
                    ])
                        ->columnSpan(1),

                    Forms\Components\Card::make()->schema([
                        AddressForm::make('address')
                            ->columnSpan('full'),
                    ])
                        ->columnSpan(1),
                ]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('List Products')->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(
                                        fn($state, callable $set) => $set(
                                            'unit_price',
                                            Product::find($state)?->price ?? 0
                                        )
                                    )
                                    ->columnSpan([
                                        'md' => 4,
                                    ]),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Price')
                                    ->disabled()
                                    ->numeric()
                                    ->mask(fn(Forms\Components\TextInput\Mask $mask) => $mask->money('', ' ', 2))
                                    ->required()
                                    ->columnSpan([
                                        'md' => 3,
                                    ]),

                                Forms\Components\TextInput::make('qty')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->rules(['integer', 'min:0'])
                                    ->default(0)
                                    ->columnSpan([
                                        'md' => 1,
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(
                                        fn(Closure $get, $state, callable $set) => $set(
                                            'total_price',
                                            Product::find($get('product_id'))?->price * (int)$state ?? 0
                                        )
                                    ),

                                Forms\Components\TextInput::make('total_price')
                                    ->label('Total Price')
                                    ->disabled()
                                    ->numeric()
//                                    ->mask(fn(Forms\Components\TextInput\Mask $mask) => $mask->money('', ' ', 2))
                                    ->required()
                                    ->columnSpan([
                                        'md' => 2,
                                    ]),

                            ])
                            ->orderable()
                            ->defaultItems(1)
                            ->disableLabel()
                            ->columns([
                                'md' => 10,
                            ])
                            ->required(),
                    ])
                ])->columnSpan('full'),
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
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'processing',
                        'success' => fn($state) => in_array($state, ['delivered', 'shipped']),
                    ]),
                Tables\Columns\TextColumn::make('currency')
                    ->getStateUsing(fn($record): ?string => Currency::find($record->currency)?->name ?? null)
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_price')
                    ->label('Shipping cost')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date()
                    ->description(fn($record) => date('H:i', strtotime($record->created_at)))
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn($state): string => 'Jan 01, ' . now()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from ' . Carbon::parse(
                                    $data['created_from']
                                )->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse(
                                    $data['created_until']
                                )->toFormattedDateString();
                        }

                        return $indicators;
                    }),
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
            PaymentsRelationManager::class
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'customer.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Order $record */

        return [
            'Customer' => optional($record->customer)->name,
        ];
    }

    protected static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['customer', 'items']);
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::$model::where('status', 'new')->count();
    }
}
