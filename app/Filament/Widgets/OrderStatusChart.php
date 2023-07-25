<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

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
        $delivered = Order::where('client_status', 'delivered')->count();
        $total = Order::count();
        if ($total != 0) {
            $percentage = ($delivered / $total) * 100;
        } else {
            $percentage = 0;
        }

        return [
            'series' => [$percentage],
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
            'colors' => ['#6366f1'],
        ];
    }

    protected function getFooter(): string|View
    {
        $order_pending = Order::where('client_status', 'PENDING')->count();
        $order_printing = Order::where('client_status', 'PRINTING')->count();
        $order_completed = Order::where('client_status', 'COMPLETED')->count();

        return view('order-status-footer', compact('order_pending', 'order_printing', 'order_completed'));
    }
}
