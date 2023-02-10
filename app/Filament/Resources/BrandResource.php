<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'shop/brands';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxValue(50),
                Forms\Components\TextInput::make('website')
                    ->url(),
                Forms\Components\MarkdownEditor::make('description')
                    ->label('Description')
                    ->columnSpan('full'),
                Forms\Components\Toggle::make('is_visible')
                    ->label('Visible to customers.')
                    ->default(true),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('website')
                    ->label('Website')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('is_visible')
                    ->label('Visibility')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created Date')
                    ->date()
                    ->sortable(),
            ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBrands::route('/'),
        ];
    }
}
