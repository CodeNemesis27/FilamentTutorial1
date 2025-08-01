<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatusEnum;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected static ?int $sort = 2;

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Customer::count())
                ->description('Increase in customers')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Products', Product::count())
                ->description('Total products in store')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Pending Orders', Order::where('status', OrderStatusEnum::PENDING->value)->count())
                ->description('Total pending orders')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
