<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class OrdersChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static string $chartId = 'ordersChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Orders Chart';

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('date_start')
                ->default(now()->startOfYear()),
            DatePicker::make('date_end')
                ->default(now()->endOfYear()),
        ];
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {

        $start = $this->filterFormData['date_start'];
        $end = $this->filterFormData['date_end'];

        $data = Trend::model(Order::class)
            ->between(
                start: Carbon::parse($start),
                end: Carbon::parse($end),
            )
            ->perMonth()
            ->count();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 253,
            ],
            'series' => [
                [
                    'name' => 'BasicBarChart',
                    'data' => $data->map(fn (trendvalue $value) => $value->aggregate),
                ],
            ],
            'xaxis' => [
                'categories' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M')),
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'colors' => ['#06DFBF'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }
}
