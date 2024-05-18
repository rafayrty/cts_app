<?php

use App\Events\SendLocation;
use App\Http\Controllers\QuizController;
use App\Models\Package;
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
//Route::get('fill-data-pdf-product/{id}', [PersonalizationController::class, 'generatePDF'])->name('preview.pdf');
Route::group(['middleware' => 'auth:filament'], function () {
    Route::get('/quiz_attempt/{id}',[QuizController::class,'quiz'])->name('quiz.attempt');
    Route::post('/quiz_attempt/{id}',[QuizController::class,'submit'])->name('quiz.submit');
    Route::get('/quiz_results/{id}',[QuizController::class,'results'])->name('quiz.results');
});
//Route::get('fill-data-pdf-document/{ids}/{order_item_id}', [PersonalizationController::class, 'generatePDFFromDocumentOrderDownload'])->name('order.download.pdf');
Route::get('/', function () {
    return redirect()->to('/admin');
});

