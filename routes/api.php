<?php

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

Route::prefix("v1")->middleware(["check_header"])->group(function () {

    // Authentication
    Route::prefix('auth')->group(function () {

        // Public
        Route::post('login', 'API\AuthController@login');
        Route::post('register', 'API\AuthController@register');
        Route::get('social/{provider}', 'API\AuthController@social');
        Route::get('social-fallback/{provider}', 'API\AuthController@socialFallback');
        Route::get('verify/{id}', 'API\AuthController@verify');
        Route::post('reset-password', 'API\AuthController@resetPassword');
        Route::post('verify/reset-password', 'API\AuthController@verifyResetPassword');
        Route::post('change-reset-password', 'API\AuthController@changeResetPassword');

        // Private
        Route::middleware(["auth:api"])->group(function () {
            Route::post('profile', 'API\AuthController@profile');
            Route::get('profile', 'API\AuthController@details');
            Route::post('address', 'API\AuthController@address');
            Route::post('password', 'API\AuthController@password');
            Route::post('logout', 'API\AuthController@logout');
        });

    });

    // Geo
    Route::prefix('geo')->group(function () {
        Route::get('countries', 'API\GeoController@countries');
        Route::get('districts', 'API\GeoController@districts');
        Route::get('search', 'API\GeoController@search');
        Route::get('location', 'API\GeoController@location');
        Route::get('cities', 'API\GeoController@cities');
    });

    // List
    Route::prefix('list')->group(function () {

        Route::get('property-types', 'API\ListController@propertyTypes');
        Route::get('property-sub-types', 'API\ListController@propertySubTypes');
        Route::get('property-features', 'API\ListController@propertyFeatures');
        Route::get('languages', 'API\ListController@languages');
        Route::get('currencies', 'API\ListController@currencies');

    });

    // Property
    Route::get('property/search', 'API\PropertyController@search');
    Route::post('property/analytics', 'API\PropertyController@analytics');
    Route::get('property-public/{id}', 'API\PropertyController@getPublic');
    Route::middleware(["auth:api", "role:agency"])->group(function () {
        Route::post('property', 'API\PropertyController@addOrEdit');
        Route::get('property/list', 'API\PropertyController@list');
        Route::get('property/{id}', 'API\PropertyController@get');
        Route::delete('property/{id}', 'API\PropertyController@delete');
    });

    // Upload
    // Property
    Route::middleware(["auth:api"])->group(function () {
        Route::post('upload-image', 'API\FileController@uploadImage');
        Route::post('remove-image', 'API\FileController@removeImage');
        Route::post('order-image', 'API\FileController@orderImage');
        Route::post('upload-profile-photo', 'API\FileController@uploadProfile');
    });

    // Page
    Route::get('page', 'API\PageController@get');

    // Contact
    Route::post('contact', 'API\MailController@contact');

    // Agency
    Route::middleware(["auth:api"])->prefix('agency')->group(function () {
        Route::post('status', 'API\Agency\AgencyController@status');

        Route::prefix('property')->group(function () {
            Route::post('summary', 'API\Agency\PropertyController@summary');
        });
    });

    // Test
    Route::get('disable', 'API\PropertyController@disableTest');

});
