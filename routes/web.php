<?php

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('fill-data-pdf', [PDFController::class, 'index']);
Route::get('fill-data-pdf-product/{id}', [PersonalizationController::class, 'generatePDF'])->name('preview.pdf');
