<?php

namespace App\Filament\Pages;

use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Filament\Widgets\Analytic\LatestOrders;
use App\Filament\Widgets\Analytic\TopCustomers;
use App\Filament\Widgets\Analytic\TopSellingItems;
use Filament\Pages\Page;
use Filament\Pages\Actions\Action;


class Analytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = "Analytics";

    protected static string $view = 'filament.pages.analytics';

//    protected function getActions(): array
//    {
//        return [
//            Action::make('settings')->action('openSettingsModal'),
//        ];
//    }
//
//    public function openSettingsModal(): void
//    {
//        $this->dispatchBrowserEvent('open-settings-modal');
//    }

    protected function getHeaderWidgets(): array
    {
        return [
            TopCustomers::class,
            TopSellingItems::class,
            LatestOrders::class
        ];
    }

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

}
