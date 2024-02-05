<?php

namespace App\Filament\Widgets;

use App\Models\Coupon;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\DatePicker;
use DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PopularPromotionsChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static string $chartId = 'PopularPromotionsChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Popular Promotions';

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
        $start = Carbon::parse($this->filterFormData['date_start'])->format('Y-m-d');
        $end = Carbon::parse($this->filterFormData['date_end'])->format('Y-m-d');

        $promotions = Coupon::select('times_used','coupon_name')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('times_used', 'desc')
            ->limit(10)
            ->get();

        $series = [];
        $products = [];
        foreach ($promotions as $promotion) {
            $series[] = $promotion->times_used;
            $products[] = $promotion->coupon_name;
        }

        $series_sum = array_sum($series);
        $series_perc =[];
        foreach($series as $serie){
                $series_perc[] = round(($serie / $series_sum) * 100,2);
        }

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => $series_perc,
            'labels' => $products,
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
        ];
    }
}

