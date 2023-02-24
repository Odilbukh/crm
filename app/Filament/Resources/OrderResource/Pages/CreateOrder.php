<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Settings\GeneralSettings;
use Filament\Resources\Pages\CreateRecord;
use Squire\Models\Currency;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCurrencyId()
    {
        $settings = new GeneralSettings();
        return Currency::find($settings->site_currency)?->id;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['user_id'] = auth()->id();

        $data['currency'] = $this->getCurrencyId();

        return $data;
    }
}
