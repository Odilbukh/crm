<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Customer;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DashboardStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $orderData = Trend::model(Order::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        $customerData = Trend::model(Customer::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            Card::make('Orders', Order::count())
                ->chart(
                    $orderData
                        ->map(fn(TrendValue $value) => $value->aggregate)
                        ->toArray()
                )
                ->color('success'),
            Card::make('Customers', Order::count())
                ->chart(
                    $customerData
                        ->map(fn(TrendValue $value) => $value->aggregate)
                        ->toArray()
                )
                ->color('success'),
            Card::make('Open orders', Order::whereIn('status', ['new', 'processing', 'shipped'])->count()),
            Card::make('Average price', number_format(Order::avg('total_price'), 2)),
        ];
    }
}