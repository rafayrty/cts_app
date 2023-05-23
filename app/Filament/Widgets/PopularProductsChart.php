<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

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

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
protected function getOptions(): array
    {
$start = '2023-01-01';
$end = '2023-12-31';
    $order_items = DB::table('order_items')
    ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
    ->whereBetween('created_at', [$start, $end])
    ->groupBy('product_id')
    ->orderBy('total_quantity', 'desc')
    ->limit(6)
    ->get();


        //dd($data);
    //$data = OrderItem::count('product_id');
    $series = [];
    $products = [];
        foreach($order_items as $order_item){

            $product = Product::find($order_item->product_id);
            if($product){
                $series[] = $order_item->total_quantity;
                $products[] = $product->product_name;
            }
        }

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => $series,
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
