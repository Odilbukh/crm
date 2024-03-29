<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Spatie\Permission\Models\Role;
use STS\FilamentImpersonate\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    protected static function getNavigationLabel(): string
    {
        return trans('filament-user::user.resource.label');
    }

    public static function getPluralLabel(): string
    {
        return trans('filament-user::user.resource.label');
    }

    public static function getLabel(): string
    {
        return trans('filament-user::user.resource.single');
    }

    protected static function getNavigationGroup(): ?string
    {
        return config('filament-user.group');
    }

    protected function getTitle(): string
    {
        return trans('filament-user::user.resource.title.resource');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                TextInput::make('name')
                    ->required()
                    ->label(trans('filament-user::user.resource.name')),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->label(trans('filament-user::user.resource.email')),
                Forms\Components\TextInput::make('password')
                    ->label(trans('filament-user::user.resource.password'))
                    ->password()
                    ->maxLength(255)
                    ->dehydrateStateUsing(static function ($state) {
                        if (!empty($state)) {
                            return Hash::make($state);
                        }
                    }),
                Forms\Components\Select::make('roles')
                    ->multiple()
                    ->searchable()
                    ->options(Role::query()->pluck('name', 'id'))
                    ->label(trans('filament-user::user.resource.roles'))
                    ->visible(fn(): bool => config('filament-user.shield')),
            ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->label(trans('filament-user::user.resource.id')),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label(trans('filament-user::user.resource.name')),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable()
                    ->label(trans('filament-user::user.resource.email')),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->boolean()
                    ->sortable()
                    ->searchable()
                    ->label(trans('filament-user::user.resource.email_verified_at')),
                TextColumn::make('roles.name')
                    ->label('Roles'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(trans('filament-user::user.resource.created_at'))
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(trans('filament-user::user.resource.updated_at'))
                    ->dateTime('d/m/Y')
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->label(trans('filament-user::user.resource.verified'))
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('unverified')
                    ->label(trans('filament-user::user.resource.unverified'))
                    ->query(fn(Builder $query): Builder => $query->whereNull('email_verified_at')),
            ]);

        if (config('filament-user.impersonate')) {
            $table->prependActions([
                Impersonate::make('impersonate'),
            ]);
        }

        return $table;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
