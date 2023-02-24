<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Analytic\LatestOrders;
use App\Filament\Widgets\Dashboard\CustomerStatDashboard;
use App\Filament\Widgets\Dashboard\DashboardStats;
use App\Filament\Widgets\Dashboard\OrderStatDashboard;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected function getWidgets(): array
    {
        return [
            DashboardStats::class,
            CustomerStatDashboard::class,
            OrderStatDashboard::class,
            LatestOrders::class,
        ];
    }
}