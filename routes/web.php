<?php

use App\Http\Controllers\Api\PersonalizationController as ApiPersonalization;
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
Route::get('order-fill-data-pdf-document/{id}/{order_item_id}', [PersonalizationController::class, 'generatePDFFromDocumentOrder'])->name('order.preview.pdf');
//Route::get('fonts/personalize-fonts.css', function () {
//$path = 'fonts.css';
//return response()->file($path);
//});
Route::get('/personalization/fonts', [ApiPersonalization::class, 'get_fonts'])->name('personalization.get-fonts');
