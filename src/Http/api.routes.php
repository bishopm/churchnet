<?php

Route::middleware(['handlecors','bindings'])->group(function () {
    Route::post('/api/login', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@login','as'=>'api.login']);
    Route::post('/api/register', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@register','as'=>'api.register']);

    // Circuits
    Route::get('api/circuits', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@index','as'=>'api.circuits.index']);
    Route::get('api/circuits/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@create','as'=>'api.circuits.create']);
    Route::get('api/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@show','as'=>'api.circuits.show']);
    Route::get('api/circuits/{circuit}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@edit','as'=>'api.circuits.edit']);
    Route::put('api/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@update','as'=>'api.circuits.update']);
    Route::post('api/circuits', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@store','as'=>'api.circuits.store']);
    Route::delete('api/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@destroy','as'=>'api.circuits.destroy']);

    // Societies
    Route::get('/api/circuits/{circuit}/societies', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@index','as'=>'api.societies.index']);
    Route::group(['middleware' => ['jwt.auth','handlecors']], function () {
        Route::get('api/check', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@check','as'=>'api.check']);

        // Meetings
        Route::get('/api/circuits/{circuit}/meetings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@index','as'=>'api.meetings.index']);
        Route::post('api/circuits/{circuit}/meetings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@store','as'=>'api.meetings.store']);
        Route::get('api/circuits/{circuit}/meetings/{meeting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@edit','as'=>'api.meetings.edit']);
        Route::put('api/circuits/{circuit}/meetings/{meeting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@update','as'=>'api.meetings.update']);
        Route::delete('api/circuits/{circuit}/meetings/{meeting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@destroy','as'=>'api.meetings.destroy']);

        // Plans
        Route::get('/api/circuits/{circuit}/plans/{year}/{quarter}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PlansController@show','as'=>'api.plans.show']);
        Route::get('/api/circuits/{circuit}/planupdate/{box}/{val}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PlansController@update','as'=>'api.plans.update']);

        // Preachers
        Route::get('/api/circuits/{circuit}/preachers', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@index','as'=>'api.preachers.index']);
        Route::get('/api/circuits/{circuit}/preachers/{preacher}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@show','as'=>'api.preachers.show']);
        Route::post('api/circuits/{circuit}/preachers', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@store','as'=>'api.preachers.store']);
        Route::put('/api/circuits/{circuit}/preachers/{preacher}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@update','as'=>'api.preachers.update']);
        Route::delete('api/circuits/{circuit}/preachers/{preacher}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@destroy','as'=>'api.preachers.destroy']);

        // Queries
        Route::post('/api/circuits/{circuit}/query', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@query','as'=>'api.circuits.query']);

        // Settings
        Route::get('/api/circuits/{circuit}/settings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@index','as'=>'api.settings.index']);
        Route::get('/api/circuits/{circuit}/settings/{setting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@show','as'=>'api.settings.show']);
        Route::post('api/circuits/{circuit}/settings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@store','as'=>'api.settings.store']);
        Route::put('/api/circuits/{circuit}/settings/{setting}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@update','as'=>'api.settings.update']);

        // Services
        Route::get('api/circuits/{circuit}/services/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@create','as'=>'api.services.create']);
        Route::get('api/circuits/{circuit}/services/{service}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@show','as'=>'api.services.show']);
        Route::post('api/circuits/{circuit}/services', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@store','as'=>'api.services.store']);
        Route::get('api/circuits/{circuit}/services/{service}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@edit','as'=>'api.services.edit']);
        Route::put('api/circuits/{circuit}/services/{service}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@update','as'=>'api.services.update']);
        Route::delete('api/circuits/{circuit}/services/{service}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@destroy','as'=>'api.services.destroy']);

        // Societies
        Route::get('api/circuits/{circuit}/societies/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@create','as'=>'api.societies.create']);
        Route::get('api/circuits/{circuit}/societies/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@show','as'=>'api.societies.show']);
        Route::post('api/circuits/{circuit}/societies', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@store','as'=>'api.societies.store']);
        Route::get('api/circuits/{circuit}/societies/{society}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@edit','as'=>'api.societies.edit']);
        Route::put('api/circuits/{circuit}/societies/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@update','as'=>'api.societies.update']);
        Route::delete('api/circuits/{circuit}/societies/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@destroy','as'=>'api.societies.destroy']);

        // Tags
        Route::get('/api/circuits/{circuit}/labels', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@index','as'=>'api.labels.index']);
        Route::get('/api/circuits/{circuit}/labels/{label}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@show','as'=>'api.labels.show']);
        Route::post('api/circuits/{circuit}/labels', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@store','as'=>'api.labels.store']);
        Route::put('/api/circuits/{circuit}/labels/{label}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@update','as'=>'api.labels.update']);
        Route::delete('api/circuits/{circuit}/labels/{label}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@destroy','as'=>'api.labels.destroy']);

        // Weekdays
        Route::get('/api/circuits/{circuit}/weekdays', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@index','as'=>'api.weekdays.index']);
        Route::get('/api/circuits/{circuit}/weekdays/{weekday}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@edit','as'=>'api.weekdays.edit']);
        Route::get('/api/circuits/{circuit}/weekdays/bydate/{date}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@show','as'=>'api.weekdays.show']);
        Route::post('api/circuits/{circuit}/weekdays', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@store','as'=>'api.weekdays.store']);
        Route::put('/api/circuits/{circuit}/weekdays/{weekday}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@update','as'=>'api.weekdays.update']);
        Route::delete('api/circuits/{circuit}/weekdays/{weekday}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@destroy','as'=>'api.weekdays.destroy']);
    });
});
