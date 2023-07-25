<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\ClientStatusEnum;
use App\Filament\Resources\OrderResource;
use App\Models\Coupon;
use App\Models\Covers;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\PrintHouseStatusEnum;
use App\Settings\GeneralSettings;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Milon\Barcode\Facades\DNS1DFacade;
use Throwable;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $payme_url = config('app.payme_url');
        $seller_payme_id = config('app.seller_payme_id');
        $payme_callback = config('app.payme_callback');

        try {
            DB::beginTransaction();
            $shipping_fee = app(GeneralSettings::class)->shipping_fee;
            $last_order = Order::orderBy('id', 'DESC')->first();
            $increment = 0;
            if ($last_order) {
                $increment = ($last_order->id + 1);
            }
            $status = 'PENDING';
            if ($data['card_payment'] == '1') {
                $buyer = $this->buyerToken($data);
                $status = 'COMPLETED';
                // Check If There are any issues in generating the Token
                if ($buyer['status_code'] != 0) {
                    throw ValidationException::withMessages(['message' => $buyer['status_error_details']]);
                    //return response()->json(['message' => $buyer['status_error_details']], 500);
                }
            }
            $order = Order::create([
                'order_numeric_id' => 1000 + $increment,
                'address' => $data['address'],
                'user_id' => $data['user_id'],
                'sub_total' => $data['sub_total'] * 100,
                'shipping' => $shipping_fee * 100,
                'coupon' => null,
                'total' => ($data['sub_total'] * 100) + ($shipping_fee * 100),
                'print_house_status' => PrintHouseStatusEnum::NEW_ORDER,
                'client_status' => ClientStatusEnum::NEW_ORDER,
                'payment_status' => $status,
                'discount_total' => $this->discountTotal($data),
                'coupon' => Coupon::find($data['coupon']) ? Coupon::find($data['coupon'])->coupon_name : '',
            ]);

            $order_items = $data['order_items'];
            foreach ($order_items as $item) {

                $prd = Product::find($item['product_id']);
                $prd->update(['sold_amount' => $prd->sold_amount + 1]);

                $cover = Covers::find($item['cover_id']);

                $cover->price = $cover->price * 100;
                $order_item = OrderItem::create([
                    'order_id' => $order->id,
                    'product_info' => $prd,
                    'product_id' => $prd->id,
                    'gender' => $prd->has_male ? 'Male' : 'Female',
                    'name' => $prd->product_name,
                    'image' => $prd->images[0],
                    'discount_total' => ($prd->price - $prd->front_price) * 100,
                    'inputs' => ['name' => $item['name'], 'age' => $item['age'], 'f_name' => $item['first_letter']],
                    'dedication' => $item['dedication'],
                    'cover' => $cover,
                    'price' => $prd->front_price * 100,
                    'total' => (($cover->price) + ($prd->front_price * 100)),
                ]);

                foreach ($prd->documents as $document) {
                    if ($document->type == ($cover->type == 2 ? 0 : 1) || $document->type == 2) {
                        $number = $order->order_numeric_id.'-'.$prd->id.'-'.$document['id'].'-'.$order_item->id;
                        $barcodes[] = ['barcode_path' => $this->barcode_generator($number), 'barcode_number' => $number];
                    }
                }
            }

            $order->update(['barcodes' => $barcodes]);

            if ($data['card_payment'] == '1') {
                $sale = $this->generateSale($data, $order, $buyer);
                if ($sale['status_code'] != 0) {
                    throw ValidationException::withMessages(['message' => $sale['status_error_details']]);
                    throw $sale['status_error_details'];
                }
                if ($sale['payme_status'] == 'success') {
                    $order->update(['payment_status' => 'COMPLETED', 'payment_info' => $sale, 'payme_sale_id' => $sale['payme_sale_id']]);
                }
            }
            DB::commit();
        } catch (Throwable $e) {
            throw $e;
            Log::error($e);
            DB::rollback();
        }

        return $order;
    }

    public function barcode_generator($number)
    {

        $file_name = 'barcodes/'.time().$number.'.png';

        $background_color = 'FFFFFF';
        $padding = 10; // Adjust the padding value as desired

        // Generate a 1D barcode (e.g., Code39)
        $barcode = DNS1DFacade::getBarcodePNGPath($number, 'C128', 1, 55, [0, 0, 0], true);

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
        [$r, $g, $b] = sscanf($background_color, '%02x%02x%02x');
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

    /**
     * Create A New Subscription
     *
     * @param  App\Http\Requests\SubscriptionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function buyerToken($data)
    {
        $seller_payme_id = config('app.seller_payme_id');
        $payme_url = config('app.payme_url');
        $payme_callback = config('app.payme_callback');

        $user = User::findOrFail($data['user_id']);
        $data = [
            'seller_payme_id' => $seller_payme_id,
            'buyer_name' => $data['cardHolderName'],
            'buyer_email' => $user->email,
            'buyer_phone' => $user->phone_number,
            'buyer_zip_code' => $data['postcode'],
            'buyer_social_id' => $data['id_number'],
            'credit_card_number' => $data['creditCard'],
            'credit_card_exp' => $data['expiry'],
            'credit_card_cvv' => $data['cvv'],
            'language' => 'en',
        ];
        // Generate Token
        $response = Http::post($payme_url.'/capture-buyer-token', $data);

        return $response;
    }

    /**
     * Create A New Sale
     *
     * @param  App\Http\Requests\OrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function generateSale($data, $order, $buyer)
    {
        $seller_payme_id = config('app.seller_payme_id');
        $payme_url = config('app.payme_url');
        $payme_callback = config('app.payme_callback');

        $order_items = $order->items;

        $items = [];
        $user = User::findOrFail($data['user_id']);
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
            'sale_email' => $user->email,
            'sale_return_url' => 'https://payme.io',
            'sale_mobile' => $user->phone_number,
            'sale_name' => $user->full_name,
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
                'email' => $user->email,
                'phone' => '+'.$order->address['country_code'] + $order->address['phone'],
                'line1' => $order->address['street_name'].' '.$order->address['street_number'].' '.$order->address['home_no'],
                'city' => $order->address['city'],
                'postal_code' => $data['postcode'],
                'country' => 'IL',
            ],
            'billing_details' => [
                'name' => $order->address['first_name'].' '.$order->address['last_name'],
                'email' => $user->email,
                'phone' => '+'.$order->address['country_code'] + $order->address['phone'],
                'line1' => $order->address['street_name'].' '.$order->address['street_number'].' '.$order->address['home_no'],
                'city' => $order->address['city'],
                'postal_code' => $data['postcode'],
                'country' => 'IL',
            ],
        ];
        Log::info($data);
        // Generate Token
        $response = Http::post($payme_url.'/generate-sale', $data);

        Log::info($response);

        return $response;
    }

    public function discountTotal($data)
    {

        $discount = 0;
        $percentage = 0;
        $sub_total = $data['sub_total'];
        if ($data['coupon']) {
            $coupon_details = Coupon::find($data['coupon']);
            if ($coupon_details) {
                $discount = ceil($sub_total * ($coupon_details->discount_percentage / 100));
                $percentage = $coupon_details->discount_percentage;
            }
        }

        return $discount * 100;
    }
}
