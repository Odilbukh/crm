<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Squire\Models\Currency;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->label('Order')
//                    ->relationship(
//                        'order',
//                        'number',
//                        fn(Builder $query, RelationManager $livewire) => $query->whereBelongsTo($livewire->ownerRecord)
//                    )
                    ->searchable()
                    ->hiddenOn('edit')
                    ->required(),

                Forms\Components\TextInput::make('reference')
                    ->columnSpan(fn(string $context) => $context === 'edit' ? 2 : 1)
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
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('provider')
                    ->options([
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                    ])
                    ->required(),

                Forms\Components\Select::make('method')
                    ->options([
                        'credit_card' => 'Credit card',
                        'bank_transfer' => 'Bank transfer',
                        'paypal' => 'PayPal',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.number')
                    ->url(fn($record) => OrderResource::getUrl('edit', [$record->order]))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reference')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->money(fn($record) => $record->currency),

                Tables\Columns\TextColumn::make('provider')
                    ->formatStateUsing(fn($state) => Str::headline($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('method')
                    ->formatStateUsing(fn($state) => Str::headline($state))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}