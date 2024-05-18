<?php

use App\Events\SendLocation;
use App\Models\Package;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\Facades\DNS1DFacade;

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
//Route::get('fill-data-pdf-product/{id}', [PersonalizationController::class, 'generatePDF'])->name('preview.pdf');
Route::group(['middleware' => 'auth:filament'], function () {
    //Route::get('fill-data-pdf-document/{id}', [PersonalizationController::class, 'generatePDFFromDocument'])->name('preview.pdf');

});
//Route::get('fill-data-pdf-document/{ids}/{order_item_id}', [PersonalizationController::class, 'generatePDFFromDocumentOrderDownload'])->name('order.download.pdf');
Route::get('/', function () {
    return redirect()->to('/admin');
});

Route::get('/map/{id}', function ($id) {

$package = Package::find($id);
$longitude = (float) $package->long;
$latitude = (float) $package->lat;
$radius = rand(1,10); // in miles

$lng_min = $longitude - $radius / abs(cos(deg2rad($latitude)) * 69);
$lng_max = $longitude + $radius / abs(cos(deg2rad($latitude)) * 69);
$lat_min = $latitude - ($radius / 69);
$lat_max = $latitude + ($radius / 69);


    $lat = $lat_max;
    $long = $lng_min;

    $location = ["lat"=>$lat, "long"=>$long];
    Package::find($id)->update(['lat'=>$lat,'long'=>$long]);
    event(new SendLocation($location));
    return response()->json(['status'=>'success', 'data'=>$location]);
});
