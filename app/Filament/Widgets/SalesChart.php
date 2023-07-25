<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SalesChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static string $chartId = 'salesChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'SalesChart';

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
        $dateStart = $this->filterFormData['date_start'];
        $dateEnd = $this->filterFormData['date_end'];
        //User Information
        $data = Trend::model(Order::class)
            ->between(
                start: Carbon::parse($dateStart),
                end: Carbon::parse($dateEnd),
            )
            ->perMonth()
            ->sum('total');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 245,
            ],
            'series' => [
                [
                    'name' => 'BasicBarChart',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate / 100),
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
            'colors' => ['#6366f1'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => true,
                ],
            ],
        ];
    }
}
