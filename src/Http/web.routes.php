<?php

Route::group(['middleware' => ['web']], function () {
    Route::get('/', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\HomeController@home','as'=>'home']);
    Route::get('login', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\LoginController@showLoginForm','as'=>'showlogin']);
    Route::post('login', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\LoginController@login','as'=>'login']);
    Route::post('password/email', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail','as'=>'sendResetLinkEmail']);
    Route::get('password/reset', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm','as'=>'showLinkRequestForm']);
    Route::post('password/reset', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ResetPasswordController@reset','as'=>'password.reset']);
    Route::get('password/reset/{token}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ResetPasswordController@showResetForm','as'=>'showResetForm']);
    Route::post('logout', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\LoginController@logout','as'=>'logout']);
    Route::get('plan/{slug}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\PlansController@plan','as'=>'plan']);

    Route::middleware(['auth'])->group(function () {
        // Settings
        Route::get('admin/settings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SettingsController@index','as'=>'admin.settings.index']);
        Route::get('admin/settings/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SettingsController@create','as'=>'admin.settings.create']);
        Route::get('admin/settings/{setting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SettingsController@show','as'=>'admin.settings.show']);
        Route::get('admin/settings/{setting}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SettingsController@edit','as'=>'admin.settings.edit']);
        Route::put('admin/settings/{setting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SettingsController@update','as'=>'admin.settings.update']);
        Route::post('admin/settings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SettingsController@store','as'=>'admin.settings.store']);
        Route::delete('admin/settings/{setting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SettingsController@destroy','as'=>'admin.settings.destroy']);
    });

    Route::middleware(['handlecors'])->group(function () {

        // Circuits
        Route::get('admin/circuits', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@index','as'=>'admin.circuits.index']);
        Route::get('admin/circuits/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@create','as'=>'admin.circuits.create']);
        Route::get('circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@show','as'=>'circuits.show']);
        Route::get('admin/circuits/{circuit}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@edit','as'=>'admin.circuits.edit']);
        Route::put('admin/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@update','as'=>'admin.circuits.update']);
        Route::post('admin/circuits', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@store','as'=>'admin.circuits.store']);
        Route::delete('admin/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@destroy','as'=>'admin.circuits.destroy']);

        // Districts
        Route::get('districts', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@index','as'=>'districts.index']);
        Route::get('districts/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@create','as'=>'districts.create']);
        Route::get('districts/{district}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@show','as'=>'districts.show']);
        Route::get('districts/{district}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@edit','as'=>'districts.edit']);
        Route::put('districts/{district}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@update','as'=>'districts.update']);
        Route::post('districts', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@store','as'=>'districts.store']);
        Route::delete('districts/{district}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@destroy','as'=>'districts.destroy']);

        // Societies
        Route::get('societies', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@index','as'=>'societies.index']);
        Route::get('societies/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@create','as'=>'societies.create']);
        Route::get('societies/{society}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@edit','as'=>'societies.edit']);
        Route::put('societies/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@update','as'=>'societies.update']);
        Route::post('societies', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@store','as'=>'societies.store']);
        Route::delete('societies/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@destroy','as'=>'societies.destroy']);
        Route::get('{circuit}/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@show','as'=>'societies.show']);
    });
});
