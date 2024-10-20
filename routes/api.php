<?php

use Illuminate\Http\Request;
use App\Http\Controllers\SslCommerzPaymentController;

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
// SSLCOMMERZ Start
Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);

Route::post('/pay', [SslCommerzPaymentController::class, 'index']);
Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

Route::post('/success', [SslCommerzPaymentController::class, 'success']);
Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);

Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
//SSLCOMMERZ END




Route::group(["namespace" => "Api\Auth"], function () {
    Route::post("login", "ApiAuthController@Login")->name("login");
    Route::post('register', "RegisterController@register")->name("register");
    Route::post('otp-verify', "RegisterController@verifyOtpAndCompleteRegistration")->name("otp-verify");
    Route::post('forgotPassword', "RegisterController@forgotPassword")->name("forgotPassword");
    Route::post('verify-otp', "RegisterController@verifyOtp")->name("verify-otp");
    Route::post('resetPassword', "RegisterController@resetPassword")->name("resetPassword");
    Route::post('logout', "ApiAuthController@logout")->middleware('auth:api');


    // Route::get('me', "ApiAuthController@Login")->middleware('auth:api');
});

Route::group(["namespace" => "Api"], function () {
    Route::get("about-us", "SettingsController@aboutUs")->name("about-us");
    Route::get("contact-us", "SettingsController@contactUs")->name("contact-us");
    Route::get("terms-and-conditions", "SettingsController@termsAndConditions")->name("terms-and-conditions");
    Route::get("privacy-policy", "SettingsController@privacyPolicy")->name("privacy-policy");
    Route::get("refund-policy", "SettingsController@refundPolicy")->name("refund-policy");
    Route::get("help-and-support", "SettingsController@helpAndSupport")->name("help-and-support");

    Route::get("get-category", "CategoryController@getCategory")->name("get-category");
    Route::get("category-wise-product/{category_id}", "CategoryController@categoryWiseProduct")->name("category-wise-product");

    Route::get("get-brands", "BrandController@getBrands")->name("get-brands");
    Route::get("brand-wise-product/{brand_id}", "BrandController@brandWiseProduct")->name("brand-wise-product");

    Route::get("get-popular-products", "ProductController@getPopularProducts")->name("get-popular-products");
    Route::get("get-latest-products", "ProductController@getLatestProducts")->name("get-latest-products");
    Route::get("get-best-selling-products", "ProductController@getBestSellingProducts")->name("get-best-selling-products");
    Route::get("get-slider", "ProductController@getSlider")->name("get-slider");
    Route::get("product-details/{product_id}", "ProductController@productDetails")->name("product-details");
    Route::post("product-filter", "ProductController@productFilter")->name("product-filter");
    Route::post("product-search", "ProductController@productSearch")->name("product-search");
    Route::post("product-search-and-filter", "ProductController@productFilterAndSearch")->name("product-search-and-filter");

    Route::post("search-suggestions", "ProductController@searchSuggestions")->name("search-suggestions");
    Route::post("add-product-review", "ProductController@addProductReview")->name("add-product-review");
    Route::post("get-product-reviews", "ProductController@getProductReviews")->name("get-product-reviews");

   
});


Route::group(["middleware" => ["auth:api", "jwt.auth"], "namespace" => "Api"], function () {

    Route::post("add-to-cart", "CartController@addToCart")->name("add-to-cart");
    Route::post("get-cart-contents", "CartController@getCartContents")->name("get-cart-contents");

    Route::get("get-profile", "ProfileController@getProfile")->name("get-profile");
    Route::post("update-profile", "ProfileController@updateProfile")->name("update-profile");
    Route::post("delivery-address", "ProfileController@deliveryAddress")->name("deliveryAddress");

    Route::get("shipping-methods", "ShippingController@shippingMethods")->name("shipping-methods");

    Route::post("check-out", "CheckOutController@checkOutApi")->name("check-out");
    Route::post("order-details", "CheckOutController@orderDetails")->name("order-details");
    Route::get("running-orders", "CheckOutController@runningOrders")->name("running-orders");
    Route::get("order-history", "CheckOutController@orderHistory")->name("order-history");
    Route::post("order-cancel", "CheckOutController@orderCancel")->name("order-cancel");

    Route::get("add-to-wishlist/{product_id}", "wishlistController@add_to_wishlist")->name("add-to-wishlist");
    Route::get("remove-to-wishlist/{product_id}", "wishlistController@remove_to_wishlist")->name("remove-to-wishlist");
    Route::get("get-wishlist", "wishlistController@get_wishlist")->name("get-wishlist");

    Route::get("get-coupons", "CouponController@getCoupons")->name("get-coupons");
    Route::post("coupon-details", "CouponController@couponDetails")->name("coupon-details");
});


// php artisan serve --host=192.168.0.114 --port=8000







// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
