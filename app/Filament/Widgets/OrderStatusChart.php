<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\ClientStatusEnum;
class OrderStatusChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static string $chartId = 'orderStatusChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Order Delivery Statuses';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        //List of Orders that have been delivered
        $done = Order::where('client_status', ClientStatusEnum::DONE)->count();
        $total = Order::count();
        if ($total != 0) {
            $percentage = round(($done / $total) * 100,2);
        } else {
            $percentage = 0;
        }
//<g id="SvgjsG6827" class="apexcharts-datalabels-group" transform="translate(0, 0) scale(1)" style="opacity: 1;"><text id="SvgjsText6828" font-family="Helvetica, Arial, sans-serif" x="108" y="190" text-anchor="middle" dominant-baseline="auto" font-size="16px" font-weight="600" fill="#6366f1" class="apexcharts-text apexcharts-datalabel-label" style="font-family: Helvetica, Arial, sans-serif;">Orders Completed</text><text id="SvgjsText6829" font-family="Helvetica, Arial, sans-serif" x="108" y="122" text-anchor="middle" dominant-baseline="auto" font-size="22px" font-weight="400" fill="#f6f7f8" class="apexcharts-text apexcharts-datalabel-value" style="font-family: Helvetica, Arial, sans-serif;">0%</text></g>
        return [
            'series' => [$percentage],

            'dataLabels'=>[
                'style'=>['colors' => ['black']]
            ],
            'chart' => [
                'height' => 240,
                'type' => 'radialBar',
                'offsetY' => -10,
            ],
            'plotOptions' => [
                'radialBar' => [
                    'startAngle' => -135,
                    'endAngle' => 135,
                    'dataLabels' => [
                        'name' => [
                            'fontSize' => '16px',
                            'offsetY' => 82,
                        ],
                        'value' => [
                            'offsetY' => -2,
                            'fontSize' => '22px',
                        ],
                    ],
                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'shadeIntensity' => 0.5,
                    'inverseColors' => false,
                    'gradientToColors' => ['#F59E0C'],
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 50],
                ],
            ],
            'stroke' => [
                'dashArray' => 4,
            ],
            'labels' => [
                'Orders Completed',
            ],
            'colors' => ['#8da12b'],
        ];
    }

    protected function getFooter(): string|View
    {
        $order_new_order = Order::where('client_status', ClientStatusEnum::NEW_ORDER)->count();
        $order_printing_status =  Order::where('client_status',ClientStatusEnum::STARTING)->count();
        $order_in_delivery = Order::where('client_status', ClientStatusEnum::IN_DELIVERY)->count();

        return view('order-status-footer', compact('order_new_order', 'order_printing_status', 'order_in_delivery'));
    }
}
