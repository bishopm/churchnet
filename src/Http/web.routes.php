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
        Route::get('methodist/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@show','as'=>'circuits.show']);
        Route::get('admin/circuits/{circuit}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@edit','as'=>'admin.circuits.edit']);
        Route::put('admin/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@update','as'=>'admin.circuits.update']);
        Route::post('admin/circuits', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@store','as'=>'admin.circuits.store']);
        Route::delete('admin/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\CircuitsController@destroy','as'=>'admin.circuits.destroy']);

        // Districts
        Route::get('methodist/districts', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@index','as'=>'districts.index']);
        Route::get('methodist/districts/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@create','as'=>'districts.create']);
        Route::get('methodist/districts/{district}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@show','as'=>'districts.show']);
        Route::get('methodist/districts/{district}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@edit','as'=>'districts.edit']);
        Route::put('methodist/districts/{district}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@update','as'=>'districts.update']);
        Route::post('methodist/districts', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@store','as'=>'districts.store']);
        Route::delete('methodist/districts/{district}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\DistrictsController@destroy','as'=>'districts.destroy']);

        // Pages
        Route::get('admin/pages', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\PagesController@index','as'=>'admin.pages.index']);
        Route::get('admin/pages/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\PagesController@create','as'=>'admin.pages.create']);
        Route::get('methodist/pages/{page}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\PagesController@show','as'=>'pages.show']);
        Route::get('admin/pages/{page}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\PagesController@edit','as'=>'admin.pages.edit']);
        Route::put('admin/pages/{page}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\PagesController@update','as'=>'admin.pages.update']);
        Route::post('admin/pages', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\PagesController@store','as'=>'admin.pages.store']);
        Route::delete('admin/pages/{page}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\PagesController@destroy','as'=>'admin.pages.destroy']);

        // Resources
        Route::get('admin/resources', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\ResourcesController@index','as'=>'admin.resources.index']);
        Route::get('admin/resources/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\ResourcesController@create','as'=>'admin.resources.create']);
        Route::get('resources/{resource}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\ResourcesController@show','as'=>'resources.show']);
        Route::get('admin/resources/{resource}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\ResourcesController@edit','as'=>'admin.resources.edit']);
        Route::get('admin/resources/addtag/{resource}/{tag}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Web\ResourcesController@addtag','as' => 'admin.resources.addtag']);
        Route::get('admin/resources/removetag/{resource}/{tag}', ['uses' => 'ishopm\Churchnet\Http\Controllers\Web\ResourcesController@removetag','as' => 'admin.resources.removetag']);
        Route::put('admin/resources/{resource}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\ResourcesController@update','as'=>'admin.resources.update']);
        Route::post('admin/resources', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\ResourcesController@store','as'=>'admin.resources.store']);
        Route::delete('admin/resources/{resource}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\ResourcesController@destroy','as'=>'admin.resources.destroy']);

        // Societies
        Route::get('methodist/societies', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@index','as'=>'societies.index']);
        Route::get('methodist/societies/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@create','as'=>'societies.create']);
        Route::get('methodist/societies/{society}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@edit','as'=>'societies.edit']);
        Route::put('methodist/societies/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@update','as'=>'societies.update']);
        Route::post('methodist/societies', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@store','as'=>'societies.store']);
        Route::delete('methodist/societies/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@destroy','as'=>'societies.destroy']);
        Route::get('methodist/{circuit}/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\SocietiesController@show','as'=>'societies.show']);

        // Tags
        Route::get('tag/{tag}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Web\HomeController@tag','as'=>'tag']);
    });
});
