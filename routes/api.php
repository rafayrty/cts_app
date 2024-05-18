<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AttributesController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\FormSubmissionController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\PersonalizationController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\WishListController;
use App\Models\FilamentPage as ModelsFilamentPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::get('/testing/{order_id}', [OrdersController::class,'external_invoice']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

});

