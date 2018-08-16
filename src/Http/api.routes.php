<?php

Route::middleware(['handlecors','bindings'])->group(function () {
    Route::post('/api/methodist/login', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@login','as'=>'api.login']);
    Route::post('/api/methodist/register', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@register','as'=>'api.register']);

    // Users
    Route::get('api/methodist/users/{user}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\UsersController@userdetails','as'=>'api.users.details']);

    // Districts
    Route::get('api/districts', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\DistrictsController@index','as'=>'api.districts.index']);
    Route::get('api/districts/{district}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\DistrictsController@show','as'=>'api.districts.show']);

    // Circuits
    Route::get('api/methodist/circuits', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@index','as'=>'api.circuits.index']);
    Route::get('api/methodist/circuits/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@create','as'=>'api.circuits.create']);
    Route::get('api/methodist/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@show','as'=>'api.circuits.show']);
    Route::get('api/methodist/circuits/{circuit}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@edit','as'=>'api.circuits.edit']);
    Route::put('api/methodist/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@update','as'=>'api.circuits.update']);
    Route::post('api/methodist/circuits', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@store','as'=>'api.circuits.store']);
    Route::delete('api/methodist/circuits/{circuit}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@destroy','as'=>'api.circuits.destroy']);

    // Groups
    Route::get('api/methodist/groups', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@index','as'=>'api.groups.index']);
    Route::get('api/methodist/groups/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@create','as'=>'api.groups.create']);
    Route::get('api/methodist/groups/{group}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@show','as'=>'api.groups.show']);
    Route::get('api/methodist/groups/{group}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@edit','as'=>'api.groups.edit']);
    Route::put('api/methodist/groups/{group}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@update','as'=>'api.groups.update']);
    Route::post('api/methodist/groups', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@store','as'=>'api.groups.store']);
    Route::post('api/methodist/groups/search', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@search','as'=>'api.groups.search']);
    Route::delete('api/methodist/groups/{group}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@destroy','as'=>'api.groups.destroy']);

    // Households
    Route::get('api/methodist/households', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@index','as'=>'api.households.index']);
    Route::get('api/methodist/households/create', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@create','as'=>'api.households.create']);
    Route::get('api/methodist/households/{household}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@show','as'=>'api.households.show']);
    Route::get('api/methodist/households/{household}/edit', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@edit','as'=>'api.households.edit']);
    Route::put('api/methodist/households/{household}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@update','as'=>'api.households.update']);
    Route::post('api/methodist/households', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@store','as'=>'api.households.store']);
    Route::post('api/methodist/households/search', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@search','as'=>'api.households.search']);
    Route::delete('api/methodist/households/{household}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@destroy','as'=>'api.households.destroy']);

    // People
    Route::post('api/methodist/people/search', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@search','as'=>'api.people.search']);
    Route::get('/api/methodist/people/{person}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@appshow','as'=>'api.people.appshow']);

    // Societies
    Route::post('/api/methodist/societies/search', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@search','as'=>'api.societies.search']);
    Route::get('/api/circuits/{circuit}/societies/thisweek', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@thisweek','as'=>'api.societies.thisweek']);
    Route::get('api/societies/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@show','as'=>'api.societies.show']);
    Route::post('api/methodist/addsociety', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@appstore','as'=>'api.societies.appstore']);

    // Journey routes
    Route::post('/api/login', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@journeylogin','as'=>'api.journey.login']);
    Route::get('api/sunday/{date?}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@sunday','as'=>'api.lectionary.sunday']);
    Route::get('api/reading/{reading}/{bible}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@reading','as'=>'api.lectionary.reading']);
    Route::get('api/feeds/ffdl', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@ffdl','as'=>'api.feeds.ffdl']);
    Route::get('/api/feeditems/{society}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@feeditems','as'=>'api.feeds.feeditems']);
    Route::post('/api/feeditems', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@store','as'=>'api.feeditems.store']);
    Route::get('api/lectionary/{lyear?}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@wholeyear','as'=>'api.lectionary.wholeyear']);
    Route::get('/api/circuits/{circuit}/upcomingmeetings', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@upcoming','as'=>'api.meetings.upcoming']);
    Route::get('/api/circuits/{circuit}/societies', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@index','as'=>'api.societies.index']);
    Route::get('/api/circuits/{circuit}/withsocieties', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@withsocieties','as'=>'api.circuits.withsocieties']);
    Route::get('/api/methodist/circuits/{circuit}/plans/currentplan', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PlansController@currentplan','as'=>'api.plans.currentplan']);
    Route::group(['middleware' => ['jwt.auth','handlecors']], function () {
        Route::post('/api/phone', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@phone','as'=>'api.individuals.phone']);
        Route::get('/api/message/{id}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@message','as'=>'api.individuals.message']);
        Route::post('/api/message', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@addmessage','as'=>'api.individuals.addmessage']);
        Route::post('api/circuits/{circuit}/preachers/phone', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@phone','as'=>'api.preachers.phone']);
        Route::post('/api/combined', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@addcombined','as'=>'api.individuals.addcombined']);
        Route::post('/api/individual', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@journeyadd','as'=>'api.individuals.journeyadd']);
        Route::post('/api/household', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@journeyedit','as'=>'api.individuals.journeyedit']);
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

        // People
        Route::get('/api/methodist/circuits/{circuit}/people', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@index','as'=>'api.people.index']);
        Route::get('/api/methodist/circuits/{circuit}/people/{person}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@show','as'=>'api.people.show']);
        Route::post('api/methodist/circuits/{circuit}/people', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@store','as'=>'api.people.store']);
        Route::put('/api/methodist/circuits/{circuit}/people/{person}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@update','as'=>'api.people.update']);
        Route::delete('api/methodist/circuits/{circuit}/people/{person}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@destroy','as'=>'api.people.destroy']);

        // Positions
        Route::get('/api/methodist/circuits/{circuit}/positions', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\TagsController@index','as'=>'api.positions.index']);
        Route::get('/api/methodist/circuits/{circuit}/positions/identify/{position}/{type}', ['uses'=>'Bishopm\Churchnet\Http\Controllers\Api\TagsController@identify','as'=>'api.positions.identify']);

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
