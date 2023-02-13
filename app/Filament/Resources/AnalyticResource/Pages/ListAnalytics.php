<?php

namespace App\Filament\Resources\AnalyticResource\Pages;

use App\Filament\Resources\AnalyticResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnalytics extends ListRecords
{
    protected static string $resource = AnalyticResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
