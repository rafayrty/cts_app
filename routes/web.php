<?php

use App\CustomDNS1D;
use App\Http\Controllers\Admin\PackagingManagementController;
use App\Http\Controllers\Admin\PrintingManagementController;
use App\Http\Controllers\Admin\UpdateOrderStatusController;
use App\Http\Controllers\Api\PersonalizationController as ApiPersonalization;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Milon\Barcode\Facades\DNS2DFacade;
use Milon\Barcode\Facades\DNS1DFacade;
use App\Http\Controllers\AutoSaveController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PersonalizationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('fill-data-pdf', [PDFController::class, 'index']);
//Route::get('fill-data-pdf-product/{id}', [PersonalizationController::class, 'generatePDF'])->name('preview.pdf');
Route::group(['middleware'=>'auth:filament'],function(){
    Route::get('fill-data-pdf-document/{id}', [PersonalizationController::class, 'generatePDFFromDocument'])->name('preview.pdf');
    Route::get('pdf-preview-document-order/{id}/{order_item_id}', [PersonalizationController::class, 'pdf_preview_document_order'])->name('order.preview.pdf');
    Route::get('pdf-download-all-document-order/{order_id}', [PersonalizationController::class, 'pdf_download_all_document_order'])->name('order.download.pdf');


    //Update Order Status
    Route::get('update_print_status/{order_id?}/{status?}', [UpdateOrderStatusController::class,'update_print_status'])->name('order.update_print.status');
    Route::get('update_client_status/{order_id?}/{status?}', [UpdateOrderStatusController::class,'update_client_status'])->name('order.update_client.status');


    //Update Order Status
    Route::get('find_book/{barcode?}', [PrintingManagementController::class,'find_book'])->name('order.find_book');
    Route::get('find_cover/{barcode?}/{book_item_id?}', [PrintingManagementController::class,'find_cover'])->name('order.find_cover');

    Route::get('find_packaging_order/{barcode?}', [PackagingManagementController::class,'find_packaging_order'])->name('order.find_packaging_order');

});
//Route::get('fill-data-pdf-document/{ids}/{order_item_id}', [PersonalizationController::class, 'generatePDFFromDocumentOrderDownload'])->name('order.download.pdf');
Route::get('/', function () {
    return redirect()->to('/admin');
});






//Route::get('fonts/personalize-fonts.css', function () {
//$path = 'fonts.css';
//return response()->file($path);
//});
Route::post('/save_product', [AutoSaveController::class, 'index']);
Route::get('/personalization/fonts', [ApiPersonalization::class, 'get_fonts'])->name('personalization.get-fonts');

Route::get('/testing',function(){

    $number ='1097-28-119-99';
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
});
Route::get('/fix_barcodes',function(){
    $orders = Order::all();

    foreach($orders as $order){
        $barcodes = $order->barcodes;
        $new_barcodes = [];
        foreach($barcodes as $db_barcode){

            $number = $db_barcode['barcode_number'];
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

            $new_barcodes[] = ['barcode_path'=>$file_name,'barcode_number'=>$number];
        }
        //Update Order Now
        Order::findOrFail($order->id)->update(['barcodes'=>$new_barcodes]);
    }
    return true;

});

