<?php

Route::middleware(['handlecors','bindings'])->group(function () {
    Route::post('/api/methodist/login', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@login','as'=>'api.login']);
    Route::post('/api/methodist/register', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@register','as'=>'api.register']);

    // Districts
    Route::get('api/methodist/districts', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\DistrictsController@index','as'=>'api.districts.index']);
    Route::get('api/methodist/districts/{district}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\DistrictsController@show','as'=>'api.districts.show']);

    // Circuits
    Route::get('api/methodist/circuits', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@index','as'=>'api.circuits.index']);
    Route::get('api/methodist/circuits/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@create','as'=>'api.circuits.create']);
    Route::get('api/methodist/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@show','as'=>'api.circuits.show']);
    Route::get('api/methodist/circuits/{circuit}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@edit','as'=>'api.circuits.edit']);
    Route::put('api/methodist/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@update','as'=>'api.circuits.update']);
    Route::post('api/methodist/circuits', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@store','as'=>'api.circuits.store']);
    Route::delete('api/methodist/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@destroy','as'=>'api.circuits.destroy']);

    // Societies
    Route::get('/api/methodist/circuits/{circuit}/societies/thisweek', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@thisweek','as'=>'api.societies.thisweek']);
    Route::get('api/methodist/circuits/{circuit}/societies/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@show','as'=>'api.societies.show']);

    // App routes
    Route::post('api/methodist/addsociety', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@appstore','as'=>'api.societies.appstore']);
    Route::get('api/sunday/{date?}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@sunday','as'=>'api.lectionary.sunday']);
    Route::get('api/reading/{reading}/{bible}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@reading','as'=>'api.lectionary.reading']);
    Route::get('api/feeds/ffdl', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@ffdl','as'=>'api.feeds.ffdl']);
    Route::get('api/lectionary/{lyear?}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@wholeyear','as'=>'api.lectionary.wholeyear']);
    Route::get('/api/methodist/circuits/{circuit}/upcomingmeetings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@upcoming','as'=>'api.meetings.upcoming']);
    Route::post('api/methodist/circuits/{circuit}/preachers/phone', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@phone','as'=>'api.preachers.phone']);
    Route::group(['middleware' => ['jwt.auth','handlecors']], function () {
        Route::get('api/methodist/check', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@check','as'=>'api.check']);

        // Meetings
        Route::get('/api/methodist/circuits/{circuit}/meetings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@index','as'=>'api.meetings.index']);
        Route::post('api/methodist/circuits/{circuit}/meetings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@store','as'=>'api.meetings.store']);
        Route::get('api/methodist/circuits/{circuit}/meetings/{meeting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@edit','as'=>'api.meetings.edit']);
        Route::put('api/methodist/circuits/{circuit}/meetings/{meeting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@update','as'=>'api.meetings.update']);
        Route::delete('api/methodist/circuits/{circuit}/meetings/{meeting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@destroy','as'=>'api.meetings.destroy']);

        // Plans
        Route::get('/api/methodist/circuits/{circuit}/plans/{year}/{quarter}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PlansController@show','as'=>'api.plans.show']);
        Route::get('/api/methodist/circuits/{circuit}/planupdate/{box}/{val}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PlansController@update','as'=>'api.plans.update']);

        // Preachers
        Route::get('/api/methodist/circuits/{circuit}/preachers', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@index','as'=>'api.preachers.index']);
        Route::get('/api/methodist/circuits/{circuit}/preachers/{preacher}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@show','as'=>'api.preachers.show']);
        Route::post('api/methodist/circuits/{circuit}/preachers', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@store','as'=>'api.preachers.store']);
        Route::put('/api/methodist/circuits/{circuit}/preachers/{preacher}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@update','as'=>'api.preachers.update']);
        Route::delete('api/methodist/circuits/{circuit}/preachers/{preacher}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@destroy','as'=>'api.preachers.destroy']);

        // Queries
        Route::post('/api/methodist/circuits/{circuit}/query', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@query','as'=>'api.circuits.query']);

        // Settings
        Route::get('/api/methodist/circuits/{circuit}/settings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@index','as'=>'api.settings.index']);
        Route::get('/api/methodist/circuits/{circuit}/settings/{setting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@show','as'=>'api.settings.show']);
        Route::post('api/methodist/circuits/{circuit}/settings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@store','as'=>'api.settings.store']);
        Route::put('/api/methodist/circuits/{circuit}/settings/{setting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@update','as'=>'api.settings.update']);

        // Services
        Route::get('api/methodist/circuits/{circuit}/services/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@create','as'=>'api.services.create']);
        Route::get('api/methodist/circuits/{circuit}/services/{service}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@show','as'=>'api.services.show']);
        Route::post('api/methodist/circuits/{circuit}/services', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@store','as'=>'api.services.store']);
        Route::get('api/methodist/circuits/{circuit}/services/{service}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@edit','as'=>'api.services.edit']);
        Route::put('api/methodist/circuits/{circuit}/services/{service}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@update','as'=>'api.services.update']);
        Route::delete('api/methodist/circuits/{circuit}/services/{service}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@destroy','as'=>'api.services.destroy']);

        // Societies
        Route::get('/api/methodist/circuits/{circuit}/societies', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@index','as'=>'api.societies.index']);
        Route::get('api/methodist/circuits/{circuit}/societies/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@create','as'=>'api.societies.create']);
        Route::post('api/methodist/circuits/{circuit}/societies', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@store','as'=>'api.societies.store']);
        Route::get('api/methodist/circuits/{circuit}/societies/{society}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@edit','as'=>'api.societies.edit']);
        Route::put('api/methodist/circuits/{circuit}/societies/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@update','as'=>'api.societies.update']);
        Route::delete('api/methodist/circuits/{circuit}/societies/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@destroy','as'=>'api.societies.destroy']);

        // Tags
        Route::get('/api/methodist/circuits/{circuit}/tags', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@index','as'=>'api.tags.index']);
        Route::get('/api/methodist/circuits/{circuit}/tags/{tag}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@show','as'=>'api.tags.show']);
        Route::post('api/methodist/circuits/{circuit}/tags', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@store','as'=>'api.tags.store']);
        Route::put('/api/methodist/circuits/{circuit}/tags/{tag}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@update','as'=>'api.tags.update']);
        Route::delete('api/methodist/circuits/{circuit}/tags/{tag}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@destroy','as'=>'api.tags.destroy']);

        // Weekdays
        Route::get('/api/methodist/circuits/{circuit}/weekdays', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@index','as'=>'api.weekdays.index']);
        Route::get('/api/methodist/circuits/{circuit}/weekdays/{weekday}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@edit','as'=>'api.weekdays.edit']);
        Route::get('/api/methodist/circuits/{circuit}/weekdays/bydate/{date}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@show','as'=>'api.weekdays.show']);
        Route::post('api/methodist/circuits/{circuit}/weekdays', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@store','as'=>'api.weekdays.store']);
        Route::put('/api/methodist/circuits/{circuit}/weekdays/{weekday}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@update','as'=>'api.weekdays.update']);
        Route::delete('api/methodist/circuits/{circuit}/weekdays/{weekday}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@destroy','as'=>'api.weekdays.destroy']);
    });
});
