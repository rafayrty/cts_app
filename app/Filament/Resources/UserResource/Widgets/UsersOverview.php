<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UsersOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $user_info = $this->get_userinfo();
        $order_info = $this->get_orderinfo();
        $sales_info = $this->get_salesinfo();
        $last_30_active_users = $this->get_last_30_active_users();
        $new_customers_today = $this->new_customers_today();

        return [
            Card::make('Customers', User::count())
                ->description($user_info->increase ? $this->convert_to_k($user_info->current_month_sum - $user_info->prev_month_sum).' increase'
                  : $this->convert_to_k($user_info->prev_month_sum - $user_info->current_month_sum).' decrease')
                ->descriptionIcon($user_info->increase ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down')
                ->chart($user_info->data_current->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color($user_info->increase ? 'success' : 'danger'),

            Card::make('Total Products', Product::count()),

            Card::make('Last 30 Days Active Users', $last_30_active_users->current_month_sum)
                ->description($last_30_active_users->increase ? $this->convert_to_k($last_30_active_users->current_month_sum - $last_30_active_users->prev_month_sum).' increase'
                  : $this->convert_to_k($last_30_active_users->prev_month_sum - $last_30_active_users->current_month_sum).' decrease')
                ->descriptionIcon($last_30_active_users->increase ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down')
                ->chart($last_30_active_users->data_current->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color($last_30_active_users->increase ? 'success' : 'danger'),

            Card::make('New Customers Today', $new_customers_today->current_month_sum)
                ->description($new_customers_today->increase ? $this->convert_to_k($new_customers_today->current_month_sum - $new_customers_today->prev_month_sum).' increase'
                  : $this->convert_to_k($new_customers_today->prev_month_sum - $new_customers_today->current_month_sum).' decrease')
                ->descriptionIcon($new_customers_today->increase ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down')
                ->chart($new_customers_today->data_current->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color($new_customers_today->increase ? 'success' : 'danger'),

            Card::make('Total Orders', Order::count())
                ->description($order_info->increase ? $this->convert_to_k($order_info->current_month_sum - $order_info->prev_month_sum).' increase'
                  : $this->convert_to_k($order_info->prev_month_sum - $order_info->current_month_sum).' decrease')
                ->descriptionIcon($order_info->increase ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down')
                ->chart($order_info->data_current->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color($order_info->increase ? 'success' : 'danger'),

            Card::make('Total Sales', '₪'.Order::sum('total') / 100)
                ->description($sales_info->increase ? '₪'.$this->convert_to_k($sales_info->current_month_sum - $sales_info->prev_month_sum).' increase'
                  : '₪'.$this->convert_to_k($sales_info->prev_month_sum - $sales_info->current_month_sum).' decrease')
                ->descriptionIcon($sales_info->increase ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down')
                ->chart($sales_info->data_current->map(fn (TrendValue $value) => $value->aggregate / 100)->toArray())
                ->color($sales_info->increase ? 'success' : 'danger'),

            Card::make('Avg Order Price', '₪'.round(Order::avg('total') / 100, 2)),
            Card::make('Total Items Sold', OrderItem::count('id')),
        ];
    }

    public function convert_to_k($number)
    {
        if ($number > 100) {
            $numberInK = number_format($number / 1000, 2).'k';
        }

        return $number;
    }

    public function new_customers_today()
    {

        $data_current = Trend::model(User::class)
            ->between(
                start: now()->startOfDay(),
                end: now()->addDay()->startOfDay(),
            )
            ->perMonth()
            ->count();

        //$data_prev = Trend::model(Order::class)
        $data_prev = Trend::model(User::class)
            ->between(
                start: now()->startOfDay(),
                end: now()->addDay()->startOfDay(),
            )
            ->perDay()
            ->count();

        $current_month_sum = array_sum($data_current->map(fn (TrendValue $value) => $value->aggregate)->toArray());
        $prev_month_sum = array_sum($data_prev->map(fn (TrendValue $value) => $value->aggregate)->toArray());
        $increase = false;
        if ($current_month_sum > $prev_month_sum) {
            $increase = true;
        }
        //dd($current_month_sum,  $prev_month_sum);

        return (object) ['increase' => $increase, 'current_month_sum' => $current_month_sum, 'prev_month_sum' => $prev_month_sum, 'data_current' => $data_current, 'data_prev' => $data_prev];

    }

    public function get_last_30_active_users()
    {

        $data_current = Trend::query(Order::distinct('user_id'))
            ->between(
                start: now()->subDays(30)->startOfDay(),
                end: now()->endOfDay(),
            )
            ->perDay()
            ->count();
        $data_prev = Trend::query(Order::distinct('user_id'))
            ->between(
                start: now()->subDays(60)->startOfDay(),
                end: now()->subDays(30)->endOfDay(),
            )
            ->perDay()
            ->count();

        $current_month_sum = array_sum($data_current->map(fn (TrendValue $value) => $value->aggregate)->toArray());
        $prev_month_sum = array_sum($data_prev->map(fn (TrendValue $value) => $value->aggregate)->toArray());
        $increase = false;
        if ($current_month_sum > $prev_month_sum) {
            $increase = true;
        }

        return (object) ['increase' => $increase, 'current_month_sum' => $current_month_sum, 'prev_month_sum' => $prev_month_sum, 'data_current' => $data_current, 'data_prev' => $data_prev];

    }

    public function get_userinfo()
    {
        //User Information
        $data_current = Trend::model(User::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();
        $data_prev = Trend::model(User::class)
            ->between(
                start: now()->subMonth()->startOfMonth(),
                end: now()->subMonth()->endOfMonth(),
            )
            ->perDay()
            ->count();

        $current_month_sum = array_sum($data_current->map(fn (TrendValue $value) => $value->aggregate)->toArray());
        $prev_month_sum = array_sum($data_prev->map(fn (TrendValue $value) => $value->aggregate)->toArray());
        $increase = false;
        if ($current_month_sum > $prev_month_sum) {
            $increase = true;
        }

        return (object) ['increase' => $increase, 'current_month_sum' => $current_month_sum, 'prev_month_sum' => $prev_month_sum, 'data_current' => $data_current, 'data_prev' => $data_prev];
    }

    public function get_orderinfo()
    {
        //User Information
        $data_current = Trend::model(Order::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();
        $data_prev = Trend::model(Order::class)
            ->between(
                start: now()->subMonth()->startOfMonth(),
                end: now()->subMonth()->endOfMonth(),
            )
            ->perDay()
            ->count();

        $current_month_sum = array_sum($data_current->map(fn (TrendValue $value) => $value->aggregate)->toArray());
        $prev_month_sum = array_sum($data_prev->map(fn (TrendValue $value) => $value->aggregate)->toArray());
        $increase = false;
        if ($current_month_sum > $prev_month_sum) {
            $increase = true;
        }

        return (object) ['increase' => $increase, 'current_month_sum' => $current_month_sum, 'prev_month_sum' => $prev_month_sum, 'data_current' => $data_current, 'data_prev' => $data_prev];
    }

    public function get_salesinfo()
    {
        //User Information
        $data_current = Trend::model(Order::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->sum('total');
        $data_prev = Trend::model(Order::class)
            ->between(
                start: now()->subMonth()->startOfMonth(),
                end: now()->subMonth()->endOfMonth(),
            )
            ->perDay()
            ->sum('total');

        $current_month_sum = array_sum($data_current->map(fn (TrendValue $value) => ($value->aggregate / 100))->toArray());
        $prev_month_sum = array_sum($data_prev->map(fn (TrendValue $value) => ($value->aggregate / 100))->toArray());
        $increase = false;
        if ($current_month_sum > $prev_month_sum) {
            $increase = true;
        }

        return (object) ['increase' => $increase, 'current_month_sum' => $current_month_sum, 'prev_month_sum' => $prev_month_sum, 'data_current' => $data_current, 'data_prev' => $data_prev];
    }
}
