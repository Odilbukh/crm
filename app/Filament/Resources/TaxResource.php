<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxResource\Pages;
use App\Filament\Resources\TaxResource\RelationManagers;
use App\Models\Tax;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'shop/taxes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required(),
                Forms\Components\TextInput::make('rate')
                    ->label('Rate')
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
                Forms\Components\Toggle::make('enabled')
                    ->label('Is active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('rate')
                    ->label('Rate'),
                Tables\Columns\BooleanColumn::make('enabled')
                    ->label('Active')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->date(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created Date')
                    ->date(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTaxes::route('/'),
        ];
    }
}
