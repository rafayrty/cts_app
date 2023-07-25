<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CustomersChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static string $chartId = 'customersChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Customers Chart';

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

        $data = Trend::model(User::class)
            ->between(
                start: Carbon::parse($dateStart),
                end: Carbon::parse($dateEnd),
            )
            ->perMonth()
            ->count();

        return [
            'chart' => [
                'type' => 'line',
                'height' => 245,
            ],
            'series' => [
                [
                    'name' => 'CustomersChart',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
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
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }
}
