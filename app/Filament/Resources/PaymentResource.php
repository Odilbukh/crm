<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Order;
use App\Models\Payment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Str;
use Squire\Models\Currency;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $slug = 'shop/payments';

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('order_id')
                        ->label('Order')
                        ->options(Order::query()->pluck('number', 'id'))
                        ->searchable(),

                    Forms\Components\TextInput::make('reference')
                        ->columnSpan(1)
                        ->required(),

                    Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->mask(fn(Forms\Components\TextInput\Mask $mask) => $mask->money('', ' ', 2))
                        ->required(),

                    Forms\Components\Select::make('currency')
                        ->getSearchResultsUsing(
                            fn(string $query) => Currency::where('name', 'like', "%{$query}%")->pluck('name', 'id')
                        )
                        ->getOptionLabelUsing(fn($value): ?string => Currency::find($value)?->getAttribute('name'))
                        ->searchable(),

                    Forms\Components\Select::make('provider')
                        ->options([
                            'click' => 'Click',
                            'payme' => 'Payme',
                            'stripe' => 'Stripe',
                            'paypal' => 'PayPal',
                        ]),

                    Forms\Components\Select::make('method')
                        ->options([
                            'cash' => 'Cash',
                            'credit_card' => 'Credit card',
                            'bank_transfer' => 'Bank transfer',
                        ]),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.number')
                    ->url(fn($record): ?string => $record->order ? OrderResource::getUrl('edit', [$record->order]) : $record->order_number)
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('amount')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('provider')
                    ->formatStateUsing(fn($state) => Str::headline($state))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('method')
                    ->formatStateUsing(fn($state) => Str::headline($state))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }    
}
