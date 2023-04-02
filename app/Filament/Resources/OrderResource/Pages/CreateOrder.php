<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\Tax;
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

        $data['total_price'] = $this->calculateOrderTotalPrice($this->data);

        return $data;
    }

    protected function calculateOrderTotalPrice(array $data): int
    {
        $totalPrice = 0;
        foreach ($data['items'] as $item)
        {
            $totalPrice += ($item['unit_price'] * $item['qty']);
        }

        $totalPrice += $this->calculateOrderTax($data['taxes'], $totalPrice);

        $totalPrice += $data['shipping_price'];

        return $totalPrice;
    }

    protected function calculateOrderTax(array $taxes, int $orderTotalPrice): int
    {
        $taxValue = 0;
        foreach ($taxes as $tax_id)
        {
            $tax = Tax::findOrFail($tax_id);
            $taxValue +=
                match ($tax->type) {
                'fixed' => $tax->rate,
                'percentage' => ($orderTotalPrice * $tax->rate) / 100,
                'compound' => 0,
                'withholding' => 0,
                default => 0
            };
        }

        return $taxValue;
    }

}