<?php

use App\CustomDNS1D;
use App\Http\Controllers\Api\PersonalizationController as ApiPersonalization;

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
Route::get('fill-data-pdf-document/{id}', [PersonalizationController::class, 'generatePDFFromDocument'])->name('preview.pdf');
Route::get('pdf-preview-document-order/{id}/{order_item_id}', [PersonalizationController::class, 'pdf_preview_document_order'])->name('order.preview.pdf');
Route::get('pdf-download-all-document-order/{order_id}', [PersonalizationController::class, 'pdf_download_all_document_order'])->name('order.download.pdf');
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

        // Generate a 1D barcode (e.g., Code39)
        $barcode = DNS1DFacade::getBarcodePNGPath($number, 'CODE11', 1, 30,array(0,0,0), true);

        // Load the generated barcode image
        $source_image = imagecreatefrompng($barcode);

        // Get image dimensions
        $width = imagesx($source_image);
        $height = imagesy($source_image);

        // Create a new image with the desired background color
        $bg_color = imagecreatetruecolor($width, $height);
        list($r, $g, $b) = sscanf($background_color, "%02x%02x%02x");
        $color = imagecolorallocate($bg_color, $r, $g, $b);
        imagefilledrectangle($bg_color, 0, 0, $width, $height, $color);

        // Merge the source image with the background image
        imagecopyresampled($bg_color, $source_image, 0, 0, 0, 0, $width, $height, $width, $height);

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
