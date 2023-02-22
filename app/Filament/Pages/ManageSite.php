<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Squire\Models\Currency;

class ManageSite extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = GeneralSettings::class;

    protected static ?string $navigationGroup = 'Settings';

    protected function getFormSchema(): array
    {
        return [
            Card::make()->schema([
                TextInput::make('site_name')
                    ->label('Site name')
                    ->required(),
                Select::make('site_currency')
                    ->getSearchResultsUsing(
                        fn(string $query) => Currency::where('name', 'like', "%{$query}%")->pluck('name', 'id')
                    )
                    ->getOptionLabelUsing(fn(GeneralSettings $settings): ?string => Currency::find($settings->site_currency)?->name ?? null)
                    ->searchable()
                    ->required(),
                Checkbox::make('site_active')
                    ->label('Site active')
                    ->required(),
            ])->columns(2)
        ];
    }
}
