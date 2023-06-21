<?php

namespace App\Http\Controllers\Api;

use App\ClientStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderProcessRequest;
use App\Mail\OrderInfoAdmin;
use App\Mail\OrderSuccess;
use App\Mail\OrderSummary;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\PrintHouseStatusEnum;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Milon\Barcode\Facades\DNS2DFacade;
use Milon\Barcode\Facades\DNS1DFacade;
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
            $last_order = Order::orderBy('id', 'DESC')->first();
            $increment = 0;
            if ($last_order) {
                $increment = ($last_order->id + 1);
            }
            $buyer = $this->buyerToken($request);
            // Check If There are any issues in generating the Token
                if ($buyer['status_code'] != 0) {

                    Log::error($buyer);
                    throw ValidationException::withMessages(['message' => $buyer['status_error_details']]);
                    //return response()->json(['message' => $buyer['status_error_details']], 500);
                }
            if ($request->shipping_fee != app(GeneralSettings::class)->shipping_fee) {
                //Check if the shipping fee is correct
                //abort(404);
            }

            $order = Order::create([
                'order_numeric_id' => 1000 + $increment,
                'address' => $request->address,
                'address_id' => $request->address['id'],
                'user_id' => $request->user()->id,
                'discount_total' => $request->discount_total * 100,
                'sub_total' => $request->subtotal * 100,
                'shipping' => $request->shipping_fee * 100,
                'coupon' => $request->coupon,
                'total' => $request->total * 100,
                'print_house_status' => PrintHouseStatusEnum::NEW_ORDER,
                'client_status' => ClientStatusEnum::NEW_ORDER,
                'payment_status' => 'PENDING',
            ]);

            $order_items = $request->items;

            $barcodes = [];
            $calculated_subtotal = 0;
            foreach ($order_items as $item) {
                $calculated_subtotal += $item['total'];
            }
            if ($request->coupon && $request->coupon != '') {
                $coupon_discount = Coupon::where('coupon_name', $request->coupon)->get()->first();
                //if (! $coupon_discount) {
                //abort(404);
                //}
                //$calculated_discount = round(($calculated_subtotal * ($coupon_discount->discount_percentage / 100)), 2);
                //approximately close
                //if (abs($calculated_discount - $request->discount_total) < 1) {
                //abort(404);
                //}
            }
            foreach ($order_items as $item) {
                $original_product = Product::findOrFail($item['id']);
                if ($original_product->front_price != $item['price'] && $original_product->front_price + $item['cover']['price'] != $item['total']) {
                    //abort(404);
                }
                //Get Documents
                //Update product sold
                $prd = Product::find($item['id']);
                $prd = $prd->update(['sold_amount' => $prd->sold_amount + 1]);

                $item['cover']['price'] = $item['cover']['price'] * 100;
                $order_item = OrderItem::create([
                    'order_id' => $order->id,
                    'product_info' => $original_product,
                    'product_id' => $item['id'],
                    'gender' => $item['gender'],
                    'name' => $item['product_name'],
                    'image' => $item['image'],
                    'discount_total' => ($original_product->price - $original_product->front_price) * 100,
                    'inputs' => $item['inputs'],
                    'dedication' => $item['dedication'],
                    'cover' => $item['cover'],
                    'price' => $item['price'] * 100,
                    'total' => $item['total'] * 100,
                ]);

                $calculated_subtotal += $item['total'];
                foreach ($original_product->documents as $document) {
                    //Type of the cover hard or soft cover generating barcodes
                    if ($document->type == ($item['cover']['type'] == 2 ? 0 : 1) || $document->type == 2) {
                        $number = $order->order_numeric_id.'-'.$original_product->id.'-'.$document['id'].'-'.$order_item->id;
                        $barcodes[] = ['barcode_path' => $this->barcode_generator($number), 'barcode_number' => $number];
                    }
                }
            }
            $order->update(['barcodes' => $barcodes]);

            $sale = $this->generateSale($request, $order, $buyer);

            if ($sale['status_code'] != 0) {

                Log::error($sale);
                throw ValidationException::withMessages(['message' => $sale['status_error_details']]);
                throw $sale['status_error_details'];
            }
            if ($sale['payme_status'] == 'success') {
                $order->update(['payment_status' => 'COMPLETED', 'payment_info' => $sale, 'payme_sale_id' => $sale['payme_sale_id']]);
            }

            //Send Order Success Email
            Mail::to(auth()->user()->email)->queue(new OrderSuccess($order));
            Mail::to(auth()->user()->email)->queue(new OrderSummary($order));

            $email = config('mail.from.address');
            Mail::to($email)->queue(new OrderInfoAdmin($order));

            //Send email to admin
            //$email = config('mail.from.address');
            //Mail::to($email)->queue(new OrderSuccess($order));

            DB::commit();
        } catch (Throwable $e) {
            throw $e;
            Log::error($e);
            DB::rollback();
        }

        return $order;
    }

    /**
     * Create A New Subscription
     *
     * @param  App\Http\Requests\SubscriptionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function buyerToken($request)
    {
        $seller_payme_id = config('app.seller_payme_id');
        $payme_url = config('app.payme_url');
        $payme_callback = config('app.payme_callback');

        $data = [
            'seller_payme_id' => $seller_payme_id,
            'buyer_name' => $request->cardHolderName,
            'buyer_email' => auth()->user()->email,
            'buyer_phone' => auth()->user()->phone_number,
            'buyer_zip_code' => $request->postcode,
            'buyer_social_id' => $request->id_number,
            'credit_card_number' => strlen(trim(str_replace(' ', '', $request->creditCard))) > 16 ? substr(trim(str_replace(' ', '', $request->creditCard)), 0, -1) : trim(str_replace(' ', '', $request->creditCard)),
            'credit_card_exp' => $request->expiry,
            'credit_card_cvv' => $request->cvv,
            'language' => 'he',
        ];
        // Generate Token
        $response = Http::post($payme_url.'/capture-buyer-token', $data);

        return $response;
    }

    /**
     * Pay Sale
     *
     * @param  App\Http\Requests\OrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function paySale($request, $order, $payme_sale_id)
    {
        $seller_payme_id = config('app.seller_payme_id');
        $payme_url = config('app.payme_url');
        $payme_callback = config('app.payme_callback');

        $order_items = $order->items;

        $data = [
            'seller_payme_id' => $seller_payme_id,
            'payme_sale_id' => $payme_sale_id,
            'sale_price' => $order->total,
            'currency' => 'ILS',
            'product_name' => 'Basmti Payment - #'.$order->order_numeric_id,
            'buyer_name' => auth()->user()->full_name,
            'buyer_email' => auth()->user()->email,
            'buyer_social_id' => $request->id_number,
            'sale_callback_url' => 'https://payme.io',
            'sale_return_url' => 'https://payme.io',
            'sale_mobile' => auth()->user()->phone_number,
            'language' => 'en',
            'credit_card_number' => trim(str_replace(' ', '', $request->creditCard)),
            'credit_card_exp' => $request->expiry,
            'credit_card_cvv' => $request->cvv,
        ];
        // Generate Token
        $response = Http::post($payme_url.'/generate-sale', $data);

        Log::info($response);

        return $response;
    }

    /**
     * Create A New Sale
     *
     * @param  App\Http\Requests\OrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function generateSale($request, $order, $buyer)
    {
        $seller_payme_id = config('app.seller_payme_id');
        $payme_url = config('app.payme_url');
        $payme_callback = config('app.payme_callback');

        $order_items = $order->items;

        $items = [];
        foreach ($order_items as $item) {
            $original_product = Product::findOrFail($item['product_id']);
            $items[] = [
                'name' => $item['name'],
                'quantity' => 1,
                'unit_price' => $item['price'],
                'total' => $item['price'],
                'discount_total' => (int) floor($original_product->price - $original_product->front_price) * 100,
                'product_code' => $original_product->id,
            ];

            $items[] = [
                'name' => $item['cover']['name'],
                'quantity' => 1,
                'unit_price' => $item['cover']['price'],
                'total' => $item['price'],
                'discount_total' => 0,
                'product_code' => $item['cover']['id'],
            ];
        }

        $data = [
            'seller_payme_id' => $seller_payme_id,
            'sale_price' => $order->total,
            'currency' => 'ILS',
            'product_name' => 'Basmti Payment - #'.$order->order_numeric_id,
            'sale_send_notification' => true,
            'sale_callback_url' => 'https://payme.io',
            'sale_email' => auth()->user()->email,
            'sale_return_url' => 'https://payme.io',
            'sale_mobile' => auth()->user()->phone_number,
            'sale_name' => auth()->user()->full_name,
            'capture_buyer' => false,
            'buyer_perform_validation' => false,
            'sale_type' => 'sale',
            'sale_payment_method' => 'credit-card',
            'layout' => 'string',
            'language' => 'en',
            'items' => $items,
            'fees' => ['shipping' => $order->shipping, 'discount' => $order->discount_total],
            'buyer_key' => $buyer['buyer_key'],
            'shipping_details' => [
                'name' => $order->address['first_name'].' '.$order->address['last_name'],
                'email' => $request->user()->email,
                'phone' => '+'.$order->address['country_code'] + $order->address['phone'],
                'line1' => $order->address['street_name'].' '.$order->address['street_number'].' '.$order->address['home_no'],
                'city' => $order->address['city'],
                'postal_code' => $request->postcode,
                'country' => 'IL',
            ],
            'billing_details' => [
                'name' => $order->address['first_name'].' '.$order->address['last_name'],
                'email' => $request->user()->email,
                'phone' => '+'.$order->address['country_code'] + $order->address['phone'],
                'line1' => $order->address['street_name'].' '.$order->address['street_number'].' '.$order->address['home_no'],
                'city' => $order->address['city'],
                'postal_code' => $request->postcode,
                'country' => 'IL',
            ],
        ];
        // Generate Token
        $response = Http::post($payme_url.'/generate-sale', $data);

        Log::info($response);

        return $response;
    }
    public function barcode_generator($number)
    {

        $file_name = 'barcodes/' . time() . $number . '.png';

        $background_color = "FFFFFF";
        $padding = 10; // Adjust the padding value as desired

        // Generate a 1D barcode (e.g., Code39)
        $barcode = DNS1DFacade::getBarcodePNGPath($number, 'C128', 1, 55,array(0,0,0), true);

        // Load the generated barcode image
        $source_image = imagecreatefrompng($barcode);

        // Get image dimensions
        $barcode_width = imagesx($source_image);
        $barcode_height = imagesy($source_image);

        // Calculate the new dimensions for the padded image
        $padded_width = $barcode_width + ($padding * 2);
        $padded_height = $barcode_height + ($padding * 2);

        // Create a new image with the desired background color and padding
        $bg_color = imagecreatetruecolor($padded_width, $padded_height);
        list($r, $g, $b) = sscanf($background_color, "%02x%02x%02x");
        $color = imagecolorallocate($bg_color, $r, $g, $b);
        imagefilledrectangle($bg_color, 0, 0, $padded_width, $padded_height, $color);

        // Calculate the position to merge the barcode image with the padded image
        $position_x = $padding;
        $position_y = $padding;

        // Merge the source image with the background image at the specified position
        imagecopy($bg_color, $source_image, $position_x, $position_y, 0, 0, $barcode_width, $barcode_height);

        // Save the final image to a temporary file
        $temp_file = tempnam(sys_get_temp_dir(), 'barcode');
        imagepng($bg_color, $temp_file);

        // Store the final image to the desired location
        Storage::disk('public')->put($file_name, file_get_contents($temp_file));

        // Clean up memory and delete the temporary file
        imagedestroy($source_image);
        imagedestroy($bg_color);
        unlink($temp_file);

        return $file_name;
    }
    //public function barcode_generator($number)
    //{
    //$file_name = 'barcodes/'.time().$number.'.png';

    //$background_color = "FFFFFF";

    //Storage::disk('public')->put($file_name, base64_decode(DNS1DFacade::getBarcodePNG($number,'C39+',1,33,array(0,0,0), true,$background_color)));

    //return $file_name;
    //}
}

