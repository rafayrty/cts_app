<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Illuminate\Support\Carbon;
use App\Models\Product;
use DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Forms\Components\DatePicker;

class PopularProductsChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static string $chartId = 'PopularProductsChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Popular Products';

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

$order_items = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
    ->whereBetween('created_at', [$start, $end])
    ->groupBy('product_id')
    ->orderBy('total_quantity', 'desc')
    ->limit(10)
    ->get();

        //dd($data);
        //$data = OrderItem::count('product_id');
        $series = [];
        $products = [];
        foreach ($order_items as $order_item) {

            $product = Product::find($order_item->product_id);
            if ($product) {
                $series[] = $order_item->total_quantity;
                $products[] = $product->product_name;
            }else{
                $series[] = $order_item->total_quantity;
                $order_find_item = OrderItem::where('product_id',$order_item->product_id)->get()->first();
                $products[] = $order_find_item->product_info['product_name'];
            }
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
