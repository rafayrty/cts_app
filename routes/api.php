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

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/user', [AuthController::class, 'update_user'])->name('user.update');

    //Address Routes
    Route::resource('address', AddressController::class);
    Route::get('/address', [AddressController::class, 'index']);

    Route::post('/review/{product_id}', [ReviewController::class, 'add_review']);
    Route::get('/review/{id}', [ReviewController::class, 'edit_review']);
    Route::delete('/review/{id}', [ReviewController::class, 'delete_review']);
    Route::put('/review/{id}', [ReviewController::class, 'update_review']);

    Route::post('/wishlist/{product_id}', [WishListController::class, 'add_wishlist'])->name('wishlist.add-wishlist');
    Route::delete('/wishlist/{product_id}', [WishListController::class, 'remove_wishlist'])->name('wishlist.remove-wishlist');
    Route::get('/wishlist', [WishListController::class, 'get_wishlist'])->name('wishlist.get-wishlist');

    //Order Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
});

Route::get('/get_shipping_fee', [OrderController::class, 'get_shipping_fee'])->name('orders.shipping_fee');

Route::get('/wishlist/check_in_wishlist/{product_id}', [WishListController::class, 'check_in_wishlist'])->name('wishlist.check-in-wishlist');
Route::post('/coupon', [CouponController::class, 'apply'])->name('coupon.apply');

/*
|--------------------------------------------------------------------------
| Products Routes
|--------------------------------------------------------------------------
*/
//Route::group(['middleware'=>'cacheResponse:300'],function() {
    Route::get('/products/most-sold-products', [ProductsController::class, 'get_most_sold_products'])->name('products.most-sold-products');
    Route::get('/products/get-featured-products', [ProductsController::class, 'get_featured_products'])->name('products.featured-products');
    Route::get('/products/get-related-products/{product_id}/{category_id}/{gender?}', [ProductsController::class, 'get_related_products'])->name('products.related-products');
    Route::get('/products/get-product-slugs', [ProductsController::class, 'get_product_slugs'])->name('products.get-product-slugs');
    Route::get('/products/get-product-attribute-options/{id}/{limit}', [ProductsController::class, 'get_product_attribute_options'])->name('products.get-product-attribute-options');
    Route::get('/products/attributes/get-all-attributes', [AttributesController::class, 'get_all_attributes'])->name('attributes.get-all-attributes');
    Route::get('/products/get-product-covers/{id}', [ProductsController::class, 'get_product_covers'])->name('products.get-product-covers');
    Route::get('/dedications/{gender}', [PersonalizationController::class, 'get_dedications'])->name('documents.get-dedications');

    Route::get('/products/product/{slug}', [ProductsController::class, 'get_product'])->name('products.get-product');
    Route::get('/products/get-products-filter', [ProductsController::class, 'get_products_filter'])->name('products.get-product-filters');

    Route::get('/categories/get-featured-categories', [CategoriesController::class, 'get_featured_categories'])->name('categories.featured-categories');
    Route::get('/categories/get-all-categories', [CategoriesController::class, 'get_all_categories'])->name('categories.get-all-categories');

    Route::get('/documents/product/{slug}', [PersonalizationController::class, 'get_document_product_slug'])->name('documents.get-document-product-slug');
    Route::get('/documents/inputs/{slug}', [PersonalizationController::class, 'get_document_inputs_slug'])->name('documents.get-document-input-slug');
    Route::get('/documents/{slug}', [PersonalizationController::class, 'get_document_info'])->name('documents.get-document-info');
//});
//Get Reviews
Route::get('/review/{id}', [ReviewController::class, 'get_product_reviews'])->name('review.get-product-reviews');

//Get Settings
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

Route::post('/leads_form/request', [FormSubmissionController::class, 'basmti_request'])->name('form.request');
Route::post('/leads_form/contact', [FormSubmissionController::class, 'basmti_contact'])->name('form.contact');
/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::name('auth.')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('verification', [AuthController::class, 'verification'])->name('verification');
    //Route::post('verification/email', [AuthController::class, 'email_verification'])->name('verification.email');
    //Route::post('resend/email', [AuthController::class, 'resend_email'])->name('resend.email');
    Route::post('resend', [AuthController::class, 'resend'])->name('resend');
    Route::post('resend_email', [AuthController::class, 'resend_email'])->name('resend_email');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('forgot', [ForgotPasswordController::class, 'forgot_password'])->name('forgot.password');
    Route::post('forgot_email', [ForgotPasswordController::class, 'forgot_password_email'])->name('forgot.password_email');
    //Route::post('forgot/phone', [ForgotPasswordController::class, 'forgot_password_phone'])->name('forgot.password.phone');
    Route::post('pass/reset', [ForgotPasswordController::class, 'reset_password'])->name('forgot.reset');
    //Route::post('pass/reset/phone', [ForgotPasswordController::class, 'reset_password_phone'])->name('forgot.reset.phone');

    //Order Routes
    //Checkout
    Route::post('orders/process_order', [OrdersController::class, 'process_order'])->name('orders.process-order');
});

Route::get('/cms/{page_slug}', function ($page_slug) {
    return ModelsFilamentPage::with('seo')->where('slug', $page_slug)->get()->first();
});
