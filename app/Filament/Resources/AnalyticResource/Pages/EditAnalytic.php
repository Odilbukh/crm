<?php

namespace App\Filament\Resources\AnalyticResource\Pages;

use App\Filament\Resources\AnalyticResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnalytic extends EditRecord
{
    protected static string $resource = AnalyticResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
