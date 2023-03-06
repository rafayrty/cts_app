<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderProcessRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\Facades\DNS2DFacade;
use Throwable;

class OrdersController extends Controller
{
    public function process_order(OrderProcessRequest $request)
    {
        $seller_payme_id = config('app.seller_payme_id');
        $payme_url = config('app.payme_url');
        $payme_callback = config('app.payme_callback');

        try {
            DB::beginTransaction();

            //Get Last Order
            $last_order = Order::latest()->first();
            $increment = 0;
            if ($last_order) {
                $increment = $last_order->id;
            }
            $order = Order::create([
                'order_numeric_id' => 1000 + $increment,
                'address' => $request->address,
                'address_id' => $request->address['id'],
                'user_id' => $request->user()->id,
                'discount_total' => $request->discount_total,
                'sub_total' => $request->subtotal,
                'shipping' => 0,
                'COUPON' => $request->coupon,
                'total' => $request->total,
                'status' => 'PENDING',
                'payment_status' => 'PENDING',
            ]);

            $order_items = $request->items;

            $barcodes = [];
            foreach ($order_items as $item) {
                $original_product = Product::findOrFail($item['id']);
                //Get Documents
                foreach ($original_product->documents as $document) {
                    $number = $order->order_numeric_id.'-'.$original_product->id.'-'.$document['id'];
                    $barcodes[] = ['barcode_path' => $this->barcode_generator($number), 'barcode_number' => $number];
                }
                //Update product sold
                $prd = Product::find($item['id']);
                $prd = $prd->update(['sold_amount' => $prd->sold_amount + 1]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_info' => $original_product,
                    'product_id' => $item['id'],
                    'gender' => $item['gender'],
                    'name' => $item['product_name'],
                    'image' => $item['image'],
                    'discount_total' => 0,
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }
            $order->update(['barcodes' => $barcodes]);
            DB::commit();
        } catch (Throwable $e) {
            throw $e;
            Log::error($e);
            DB::rollback();
        }

        return $request->all();
    }

      public function barcode_generator($number)
      {
          $file_name = 'barcodes/'.time().$number.'.png';

          Storage::disk('public')->put($file_name, base64_decode(DNS2DFacade::getBarcodePNG($number, 'PDF417')));

          return $file_name;
      }
}
