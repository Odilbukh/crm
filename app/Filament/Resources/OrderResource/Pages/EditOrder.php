<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Settings\GeneralSettings;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Squire\Models\Currency;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

//    protected function mutateFormDataBeforeSave(array $data): array
//    {
//
//        return $data;
//    }
}
