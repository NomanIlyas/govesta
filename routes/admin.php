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

// Authentication
Route::match(array('GET', 'POST'), 'login', 'Admin\AuthController@login');

// Private
Route::middleware([ "auth", "role:admin"])->group(function () {

    // Auth
    Route::get('logout', 'Admin\AuthController@logout');

    // Dashboard
    Route::get('dashboard', 'Admin\DashboardController@index');

    // Page
    Route::get('page/list', 'Admin\PageController@index');
    Route::match(array('GET', 'POST'), 'page/create', 'Admin\PageController@create');
    Route::match(array('GET', 'POST'), 'page/edit', 'Admin\PageController@edit');
    Route::get('page/translations', 'Admin\PageController@translations');

    // Country
    Route::get('country/list', 'Admin\CountryController@index');
    Route::get('country/status/{id}/{status}', 'Admin\CountryController@status');

    // State
    Route::get('state/list', 'Admin\StateController@index');
    Route::match(array('GET', 'POST'), 'state/create', 'Admin\StateController@create');
    Route::match(array('GET', 'POST'), 'state/edit', 'Admin\StateController@edit');
    Route::get('state/status/{id}/{status}', 'Admin\StateController@status');
    Route::get('state/delete', 'Admin\StateController@delete');
    Route::get('state/translation/list', 'Admin\StateController@translationList');
    Route::match(array('GET', 'POST'), 'state/translation/edit', 'Admin\StateController@translationEdit');
    Route::match(array('GET', 'POST'), 'state/translation/create', 'Admin\StateController@translationCreate');

    // City
    Route::get('city/list', 'Admin\CityController@index');
    Route::match(array('GET', 'POST'), 'city/create', 'Admin\CityController@create');
    Route::match(array('GET', 'POST'), 'city/edit', 'Admin\CityController@edit');
    Route::get('city/status/{id}/{status}', 'Admin\CityController@status');
    Route::get('city/delete', 'Admin\CityController@delete');
    Route::get('city/translation/list', 'Admin\CityController@translationList');
    Route::match(array('GET', 'POST'), 'city/translation/edit', 'Admin\CityController@translationEdit');
    Route::match(array('GET', 'POST'), 'city/translation/create', 'Admin\CityController@translationCreate');

    // District
    Route::get('district/list', 'Admin\DistrictController@index');
    Route::match(array('GET', 'POST'), 'district/create', 'Admin\DistrictController@create');
    Route::match(array('GET', 'POST'), 'district/edit', 'Admin\DistrictController@edit');
    Route::get('district/delete', 'Admin\DistrictController@delete');
    Route::get('district/status/{id}/{status}', 'Admin\DistrictController@status');

    // Property Features
    Route::prefix('property')->group(function () {

        // Feature
        Route::prefix('feature')->group(function () {
            Route::get('list', 'Admin\Property\FeatureController@index');
            Route::get('delete', 'Admin\Property\FeatureController@delete');
            // Translation
            Route::prefix('translation')->group(function () {
                Route::get('list', 'Admin\Property\FeatureController@translationList');
                Route::match(array('GET', 'POST'), 'create', 'Admin\Property\FeatureController@translationCreate');
                Route::match(array('GET', 'POST'), 'edit', 'Admin\Property\FeatureController@translationEdit');
                Route::get('delete', 'Admin\Property\FeatureController@translationDelete');
            });
        });

         // Type
         Route::prefix('type')->group(function () {
            Route::get('list', 'Admin\Property\TypeController@index');
            Route::get('delete', 'Admin\Property\TypeController@delete');
            // Translation
            Route::prefix('translation')->group(function () {
                Route::get('list', 'Admin\Property\TypeController@translationList');
                Route::match(array('GET', 'POST'), 'create', 'Admin\Property\TypeController@translationCreate');
                Route::match(array('GET', 'POST'), 'edit', 'Admin\Property\TypeController@translationEdit');
                Route::get('delete', 'Admin\Property\TypeController@translationDelete');
            });
        });

        // Sub Type
        Route::prefix('stype')->group(function () {
            Route::get('list', 'Admin\Property\SubTypeController@index');
            Route::get('delete', 'Admin\Property\SubTypeController@delete');
            // Translation
            Route::prefix('translation')->group(function () {
                Route::get('list', 'Admin\Property\SubTypeController@translationList');
                Route::match(array('GET', 'POST'), 'create', 'Admin\Property\SubTypeController@translationCreate');
                Route::match(array('GET', 'POST'), 'edit', 'Admin\Property\SubTypeController@translationEdit');
                Route::get('delete', 'Admin\Property\SubTypeController@translationDelete');
            });
        });
    });

     // District
     Route::prefix('agency')->group(function () {
        Route::get('list', 'Admin\AgencyController@index');
        Route::match(array('GET', 'POST'), 'edit', 'Admin\AgencyController@edit');
        Route::get('status', 'Admin\AgencyController@status');
        Route::match(array('GET', 'POST'), 'delete', 'Admin\AgencyController@delete');
    });
    
});

// Test
Route::get('test', 'Admin\TestController@index');
