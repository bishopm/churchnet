<?php

Route::get('/api/test/{reading}/{translation}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@reading', 'as' => 'api.test']);
Route::post('/api/push', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PushController@store', 'as' => 'api.push.store']);
Route::post('/api/churchnet/login', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@login', 'as' => 'api.login']);
Route::post('/api/synodlogin', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@synodlogin', 'as' => 'api.synodlogin']);
Route::post('/api/register', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@register', 'as' => 'api.register']);

// Journey routes
Route::post('/api/login', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@login', 'as' => 'api.login']);
Route::post('/api/checkphone', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@checkphone', 'as' => 'api.individuals.checkphone']);
Route::get('api/sunday/{society?}/{date?}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@sunday', 'as' => 'api.lectionary.sunday']);
Route::get('api/reading/{reading}/{bible}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@reading', 'as' => 'api.lectionary.reading']);
Route::get('api/readingplans', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@readingplans', 'as' => 'api.lectionary.readingplans']);
Route::get('api/dailyreading/{plan}/{id}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@dailyreading', 'as' => 'api.lectionary.dailyreading']);
Route::post('api/userfeed', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@userfeed', 'as' => 'api.feeds.user']);
Route::post('api/societies/useradded', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@useradded', 'as' => 'api.societies.useradded']);
Route::post('/api/feeditemlist', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@feeditems', 'as' => 'api.feeds.feeditems']);
Route::get('/api/feedlibrary/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@feedlibrary', 'as' => 'api.feeds.feedlibrary']);
Route::get('/api/hymns/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@hymns', 'as' => 'api.feeds.hymns']);
Route::get('/api/videos/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@videos', 'as' => 'api.feeds.videos']);
Route::get('/api/feedpost/{post}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@feedpost', 'as' => 'api.feeds.feedpost']);
Route::get('/api/feeditem/{id}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@feeditem', 'as' => 'api.feeds.feeditem']);
Route::post('/api/feedarchive', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@archive', 'as' => 'api.feeds.archive']);
Route::post('/api/myfeeditems', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@myfeeds', 'as' => 'api.feeditems.myfeeds']);
Route::post('/api/reminders', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RemindersController@show', 'as' => 'api.reminders.show']);
Route::post('/api/reminders/delete', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RemindersController@destroy', 'as' => 'api.reminders.destroy']);
Route::get('api/lectionary/{lyear?}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\LectionaryController@wholeyear', 'as' => 'api.lectionary.wholeyear']);
Route::get('/api/upcomingmeetings/{scope}/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@upcoming', 'as' => 'api.meetings.upcoming']);
Route::get('/api/independents', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@independents', 'as' => 'api.societies.independents']);
Route::get('/api/circuits/{circuit}/societies', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@index', 'as' => 'api.societies.index']);
Route::get('/api/circuits/{circuit}/withsocieties', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@withsocieties', 'as' => 'api.circuits.withsocieties']);
Route::get('/api/circuits/{circuit}/societies/thisweek', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@thisweek', 'as' => 'api.societies.thisweek']);
Route::get('api/circuits/{circuit}/societies/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@show', 'as' => 'api.societies.show']);
Route::get('api/services/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@index', 'as' => 'api.services.index']);
Route::get('/api/denominations', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\DenominationsController@index', 'as' => 'api.denominations.index']);
Route::get('/api/denominations/{id}/societies', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\DenominationsController@societies', 'as' => 'api.denominations.societies']);
Route::post('groupreport', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Web\PlansController@groupreport', 'as' => 'groupreport.show']);

// Public routes
Route::get('api/circuits/{circuit}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@show', 'as' => 'api.circuits.show']);
Route::get('api/circuits/map/{circuit}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@showwithmap', 'as' => 'api.circuits.showwithmap']);
Route::post('/api/meetings/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@index', 'as' => 'api.meetings.index']);
Route::get('api/societies/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@show', 'as' => 'api.societies.show']);
Route::get('api/journeysettings/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@journeysettings', 'as' => 'api.societies.journeysettings']);
Route::post('/api/synods', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SynodsController@index', 'as' => 'api.synods.index']);
Route::post('/api/feedback', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SynodsController@feedback', 'as' => 'api.synods.feedback']);
Route::post('/api/documents/upload', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\DocumentsController@store', 'as' => 'api.documents.store']);

// Districts
Route::get('api/districts', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\DistrictsController@index', 'as' => 'api.districts.index']);
Route::get('api/districts/{district}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\DistrictsController@show', 'as' => 'api.districts.show']);
Route::get('api/districts/map/{district}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\DistrictsController@showwithmap', 'as' => 'api.districts.showwithmap']);
Route::get('api/districts/{district}/details', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\DistrictsController@details', 'as' => 'api.districts.details']);
Route::post('api/districts/directory', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\DistrictsController@directory', 'as' => 'api.districts.directory']);

Route::group(['middleware' => ['auth:sanctum', 'ispermitted']], function () {
    Route::get('api/users/{user}/{auth?}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\UsersController@userdetails', 'as' => 'api.users.details']);
    // Journey routes
    Route::post('/api/phone', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@phone', 'as' => 'api.individuals.phone']);
    Route::get('/api/message/{id}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@message', 'as' => 'api.individuals.message']);
    Route::post('/api/message', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@addmessage', 'as' => 'api.individuals.addmessage']);
    // Route::post('api/circuits/{circuit}/preachers/phone', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@phone', 'as' => 'api.preachers.phone']);
    Route::post('/api/combined', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@addcombined', 'as' => 'api.individuals.addcombined']);
    Route::post('/api/individual', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@journeyadd', 'as' => 'api.individuals.journeyadd']);
    Route::post('/api/household', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@journeyedit', 'as' => 'api.individuals.journeyedit']);
    Route::post('/api/mysubscriptions', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@mysubscriptions', 'as' => 'api.feeds.mysubscriptions']);

    // Attendances
    Route::post('api/attendances', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\AttendancesController@store', 'as' => 'api.attendances.store']);

    // Circuits
    Route::get('api/circuits', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@index', 'as' => 'api.circuits.index']);
    Route::get('api/circuits/create', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@create', 'as' => 'api.circuits.create']);
    Route::get('api/circuits/{circuit}/edit', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@edit', 'as' => 'api.circuits.edit']);
    Route::post('api/circuits/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@search', 'as' => 'api.circuits.search']);
    Route::post('api/circuits/{circuit}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@update', 'as' => 'api.circuits.update']);
    Route::post('api/circuits', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@store', 'as' => 'api.circuits.store']);
    Route::delete('api/circuits/{circuit}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@destroy', 'as' => 'api.circuits.destroy']);

    // Feed items
    Route::post('/api/feeditems', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@store', 'as' => 'api.feeditems.store']);
    Route::post('/api/feeditems/update', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\FeedsController@update', 'as' => 'api.feeditems.update']);

    // Giving
    Route::get('/api/payments/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PaymentsController@index', 'as' => 'api.payments.index']);
    Route::post('api/payments', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PaymentsController@store', 'as' => 'api.payments.store']);
    Route::post('api/payments/{payment}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PaymentsController@edit', 'as' => 'api.payments.edit']);
    Route::post('api/payments/{payment}/update', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PaymentsController@update', 'as' => 'api.payments.update']);
    Route::delete('api/payments/{payment}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PaymentsController@destroy', 'as' => 'api.payments.destroy']);
    Route::post('/api/givingstats', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PaymentsController@stats', 'as' => 'api.payments.stats']);

    // Groups
    Route::get('api/groups', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@index', 'as' => 'api.groups.index']);
    Route::get('api/groups/create', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@create', 'as' => 'api.groups.create']);
    Route::get('api/groups/{group}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@show', 'as' => 'api.groups.show']);
    Route::get('api/groups/{group}/edit', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@edit', 'as' => 'api.groups.edit']);
    Route::post('api/groups/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@search', 'as' => 'api.groups.search']);
    Route::post('api/groups/{group}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@update', 'as' => 'api.groups.update']);
    Route::post('api/groups', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@store', 'as' => 'api.groups.store']);
    Route::post('api/groups/{group}/remove', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@remove', 'as' => 'api.groups.remove']);
    Route::post('api/groups/{group}/add', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@add', 'as' => 'api.groups.add']);
    Route::post('api/groups/{group}/sync', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@sync', 'as' => 'api.groups.sync']);
    Route::delete('api/groups/{group}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@destroy', 'as' => 'api.groups.destroy']);
    Route::get('api/groupsignups/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@signups', 'as' => 'api.groups.signups']);
    Route::post('api/groupsignupmessage', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@signupmessage', 'as' => 'api.groups.signupmessage']);

    // Guests
    Route::post('api/guests', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GuestsController@index', 'as' => 'api.guests.index']);
    Route::post('api/guests/add', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GuestsController@store', 'as' => 'api.guests.store']);

    // Households
    Route::get('api/households', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@index', 'as' => 'api.households.index']);
    Route::get('api/households/create', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@create', 'as' => 'api.households.create']);
    Route::get('api/households/{household}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@show', 'as' => 'api.households.show']);
    Route::get('api/households/{household}/edit', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@edit', 'as' => 'api.households.edit']);
    Route::post('api/households/delete', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@destroy', 'as' => 'api.households.destroy']);
    Route::post('api/households', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@store', 'as' => 'api.households.store']);
    Route::post('api/households/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@search', 'as' => 'api.households.search']);
    Route::post('api/households/{household}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@update', 'as' => 'api.households.update']);

    // Individuals
    Route::get('api/givers/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@givers', 'as' => 'api.individuals.givers']);
    Route::get('api/leaders/{circuit}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@leaders', 'as' => 'api.individuals.leaders']);
    Route::post('api/leaders', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@editleaders', 'as' => 'api.individuals.editleaders']);
    Route::post('api/giving', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@giving', 'as' => 'api.individuals.giving']);
    Route::post('api/updategiving', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@updategiving', 'as' => 'api.individuals.updategiving']);
    Route::get('api/individuals/church/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@church', 'as' => 'api.individuals.church']);
    Route::post('api/individuals/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@search', 'as' => 'api.individuals.search']);
    Route::post('api/individuals/searchnp', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@searchnonpreachers', 'as' => 'api.individuals.searchnp']);
    Route::post('api/individuals/searchgp', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@searchguestpreachers', 'as' => 'api.individuals.searchgp']);
    Route::post('api/individuals', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@store', 'as' => 'api.individuals.store']);
    Route::post('api/individuals/delete/{individual}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@destroy', 'as' => 'api.individuals.destroy']);
    Route::post('api/individuals/{individual}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@update', 'as' => 'api.individuals.update']);
    Route::post('api/individuals/{individual}/image', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\IndividualsController@image', 'as' => 'api.individuals.image']);

    // Meetings
    Route::post('api/meetings', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@store', 'as' => 'api.meetings.store']);
    Route::post('api/agendaitems', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@storeagendaitems', 'as' => 'api.meetings.storeagendaitems']);
    Route::post('api/meetings/{meeting}/update', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@update', 'as' => 'api.meetings.update']);
    Route::get('api/meetings/{meeting}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@edit', 'as' => 'api.meetings.edit']);
    Route::delete('api/meetings/{meeting}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\MeetingsController@destroy', 'as' => 'api.meetings.destroy']);

    // Messages
    Route::post('api/messages', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\MessagesController@send', 'as' => 'api.messages.send']);
    Route::post('api/messages/smscredits', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\MessagesController@getsmscredits', 'as' => 'api.messages.smscredits']);

    // Pastorals
    Route::post('api/pastorals', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PastoralsController@update', 'as' => 'api.pastorals.update']);
    Route::post('api/pastorals/{id}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PastoralsController@destroy', 'as' => 'api.pastorals.destroy']);

    // Anniversaries
    Route::post('api/specialdays', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SpecialdaysController@update', 'as' => 'api.specialdays.update']);
    Route::post('api/specialdays/{id}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SpecialdaysController@destroy', 'as' => 'api.specialdays.destroy']);

    // People
    Route::get('/api/circuits/{circuit}/people', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@index', 'as' => 'api.people.index']);
    Route::get('/api/circuits/{circuit}/people/{person}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@show', 'as' => 'api.people.show']);
    Route::post('api/circuits/{circuit}/people', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@store', 'as' => 'api.people.store']);
    Route::put('/api/circuits/{circuit}/people/{person}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@update', 'as' => 'api.people.update']);
    Route::delete('api/people/{person}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@destroy', 'as' => 'api.people.destroy']);
    Route::post('api/guests/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@guestsearch', 'as' => 'api.people.guestsearch']);
    Route::post('api/people/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@search', 'as' => 'api.people.search']);
    Route::get('/api/people/{person}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@appshow', 'as' => 'api.people.appshow']);
    Route::post('/api/circuits/{circuit}/people/{id}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PeopleController@update', 'as' => 'api.people.update']);

    // Plans
    Route::get('/api/circuits/{circuit}/mplans/monthlyplan/{year}/{month}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PlansController@monthlyplan', 'as' => 'api.plans.monthlyplan']);
    Route::get('/api/circuits/{circuit}/guestpreachers/{slug}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PlansController@guestpreachers', 'as' => 'api.plans.guestpreachers']);
    Route::get('/api/circuits/{circuit}/plans/{year}/{quarter}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PlansController@show', 'as' => 'api.plans.show']);
    Route::get('/api/circuits/{circuit}/planupdate/{box}/{val}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PlansController@update', 'as' => 'api.plans.update']);
    Route::post('/api/circuits/{circuit}/updateplan', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PlansController@updateplan', 'as' => 'api.plans.updateplan']);

    // Positions
    Route::get('/api/circuits/{circuit}/positions', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\TagsController@index', 'as' => 'api.positions.index']);
    Route::get('/api/circuits/{circuit}/positions/identify/{position}/{type}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\TagsController@identify', 'as' => 'api.positions.identify']);

    // Preachers
    // Route::get('/api/circuits/{circuit}/preachers', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@index', 'as' => 'api.preachers.index']);
    // Route::get('/api/circuits/{circuit}/preachers/{preacher}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@show', 'as' => 'api.preachers.show']);
    // Route::post('api/circuits/{circuit}/preachers', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@store', 'as' => 'api.preachers.store']);
    // Route::put('/api/circuits/{circuit}/preachers/{preacher}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@update', 'as' => 'api.preachers.update']);
    // Route::delete('api/circuits/{circuit}/preachers/{preacher}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\PreachersController@destroy', 'as' => 'api.preachers.destroy']);

    // Queries
    Route::post('/api/circuits/{circuit}/query', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\CircuitsController@query', 'as' => 'api.circuits.query']);

    // Rosters
    Route::post('/api/rosterlist', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@index', 'as' => 'api.rosters.index']);
    Route::get('/api/rosters/{id}/{year}/{month}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@show', 'as' => 'api.rosters.show']);
    Route::get('/api/rostermessages/{id}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@messages', 'as' => 'api.rosters.messages']);
    Route::post('/api/rostermessages', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@sendmessages', 'as' => 'api.rosters.sendmessages']);
    Route::get('api/rosters/{roster}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@edit', 'as' => 'api.rosters.edit']);
    Route::post('api/rosters/{roster}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@update', 'as' => 'api.rosters.update']);
    Route::post('api/rosters', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@store', 'as' => 'api.rosters.store']);
    Route::post('api/rosteritems', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@storeitem', 'as' => 'api.rosters.storeitem']);
    Route::post('api/rostergroups', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@storerostergroup', 'as' => 'api.rosters.storerostergroup']);
    Route::delete('api/rostergroups/{id}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@deleterostergroup', 'as' => 'api.rosters.deleterostergroup']);

    // Services
    Route::get('api/circuits/{circuit}/services/create', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@create', 'as' => 'api.services.create']);
    Route::get('api/circuits/{circuit}/services/{service}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@show', 'as' => 'api.services.show']);
    Route::post('api/circuits/{circuit}/services', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@store', 'as' => 'api.services.store']);
    Route::get('api/circuits/{circuit}/services/{service}/edit', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@edit', 'as' => 'api.services.edit']);
    Route::post('api/circuits/{circuit}/services/{service}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@update', 'as' => 'api.services.update']);
    Route::delete('api/circuits/{circuit}/services/{service}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\ServicesController@destroy', 'as' => 'api.services.destroy']);

    // Settings
    Route::get('/api/circuits/{circuit}/settings', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@index', 'as' => 'api.settings.index']);
    Route::get('/api/circuits/{circuit}/settings/{setting}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@show', 'as' => 'api.settings.show']);
    Route::post('api/circuits/{circuit}/settings', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@store', 'as' => 'api.settings.store']);
    Route::put('/api/circuits/{circuit}/settings/{setting}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SettingsController@update', 'as' => 'api.settings.update']);

    // Societies
    Route::post('/api/societies/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@search', 'as' => 'api.societies.search']);
    Route::post('/api/societies/settings', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@settings', 'as' => 'api.societies.settings']);
    Route::post('api/addsociety', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@appstore', 'as' => 'api.societies.appstore']);
    Route::post('api/societies/update', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@update', 'as' => 'api.societies.update']);

    // Societies
    Route::get('api/circuits/{circuit}/societies/create', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@create', 'as' => 'api.societies.create']);
    Route::post('api/circuits/{circuit}/societies', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@store', 'as' => 'api.societies.store']);
    Route::get('api/circuits/{circuit}/societies/{society}/edit', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@edit', 'as' => 'api.societies.edit']);
    Route::put('api/circuits/{circuit}/societies/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@update', 'as' => 'api.societies.update']);
    Route::delete('api/circuits/{circuit}/societies/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SocietiesController@destroy', 'as' => 'api.societies.destroy']);

    // Statistics
    Route::get('api/statistics/{society}/{yr}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\StatisticsController@index', 'as' => 'api.statistics.index']);
    Route::get('api/discipleship/{society}/{yr}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\StatisticsController@discipleship', 'as' => 'api.statistics.discipleship']);
    Route::get('api/statistics/{society}/getfordate/{yr}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\StatisticsController@getfordate', 'as' => 'api.statistics.getfordate']);
    Route::post('api/statistics', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\StatisticsController@store', 'as' => 'api.statistics.store']);

    // Synod
    Route::post('api/bluebookimage', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SynodsController@bluebookimage', 'as' => 'api.synods.bluebookimage']);
    Route::post('api/synoddocs', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\SynodsController@synoddocs', 'as' => 'api.synods.synoddocs']);

    // Tags
    Route::get('/api/circuits/{circuit}/tags', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@index', 'as' => 'api.tags.index']);
    Route::get('/api/circuits/{circuit}/tags/{tag}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@show', 'as' => 'api.tags.show']);
    Route::post('api/circuits/{circuit}/tags', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@store', 'as' => 'api.tags.store']);
    Route::put('/api/circuits/{circuit}/tags/{tag}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@update', 'as' => 'api.tags.update']);
    Route::delete('api/circuits/{circuit}/tags/{tag}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\LabelsController@destroy', 'as' => 'api.tags.destroy']);
    Route::get('/api/tags', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\TagsController@appindex', 'as' => 'api.tags.appindex']);

    // Users
    Route::post('api/users/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\UsersController@index', 'as' => 'api.users.index']);
    Route::post('api/users', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\UsersController@store', 'as' => 'api.users.store']);
    Route::post('api/permissibles', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\UsersController@permissibles', 'as' => 'api.users.permissibles']);
    Route::post('api/permissibles/delete', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\UsersController@deletepermissibles', 'as' => 'api.users.deletepermissibles']);
    Route::get('api/check', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Auth\ApiAuthController@check', 'as' => 'api.check']);

    // Venues
    Route::get('api/venues/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuesController@index', 'as' => 'api.venues.index']);
    Route::get('api/venues/create', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuesController@create', 'as' => 'api.venues.create']);
    Route::get('api/venues/{venue}/edit', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuesController@edit', 'as' => 'api.venues.edit']);
    Route::post('api/venues/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuesController@search', 'as' => 'api.venues.search']);
    Route::post('api/venues/{venue}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuesController@update', 'as' => 'api.venues.update']);
    Route::post('api/venues', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuesController@store', 'as' => 'api.venues.store']);
    Route::delete('api/venues/{venue}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuesController@destroy', 'as' => 'api.venues.destroy']);

    // Venuebookings
    Route::get('api/venuebookings/{society}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuebookingsController@index', 'as' => 'api.venuebookings.index']);
    Route::get('api/venuebookings/create', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuebookingsController@create', 'as' => 'api.venuebookings.create']);
    Route::get('api/venuebookings/{venuebooking}/edit', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuebookingsController@edit', 'as' => 'api.venuebookings.edit']);
    Route::post('api/venuebookings/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuebookingsController@search', 'as' => 'api.venuebookings.search']);
    Route::post('api/venuebookings/{venuebooking}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuebookingsController@update', 'as' => 'api.venuebookings.update']);
    Route::post('api/venuebookings', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuebookingsController@store', 'as' => 'api.venuebookings.store']);
    Route::delete('api/venuebookings/{venuebooking}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\VenuebookingsController@destroy', 'as' => 'api.venuebookings.destroy']);


    // Weekdays
    Route::post('/api/weekdays/search', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@index', 'as' => 'api.weekdays.index']);
    Route::get('/api/weekdays/{weekday}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@edit', 'as' => 'api.weekdays.edit']);
    Route::get('/api/weekdays/bydate/{date}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@show', 'as' => 'api.weekdays.show']);
    Route::post('/api/weekdays/{weekday}/update', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@update', 'as' => 'api.weekdays.update']);
    Route::post('api/weekdays', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@store', 'as' => 'api.weekdays.store']);
    Route::delete('api/weekdays/{weekday}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\WeekdaysController@destroy', 'as' => 'api.weekdays.destroy']);
});
Route::group(['middleware' => ['isspecial']], function () {
    // Special access
    Route::get('/api/specialrosters/{id}/{year}/{month}/{society_id}/{user_id}/{accesstype}/{token}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@show', 'as' => 'api.rosters.show']);
    Route::get('/api/specialrosters/thissunday/{id}/{society_id}/{accesstype}/{token}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@thisweek', 'as' => 'api.rosters.thisweek']);
    Route::post('/api/specialrosteritems', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\RostersController@storeitem', 'as' => 'api.rosters.storeitem']);
    Route::get('/api/specialgroups/{group}/{society_id}/{user_id}/{accesstype}/{token}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\GroupsController@show', 'as' => 'api.groups.show']);
    Route::get('/api/specialaccess/{society_id}/{accesstype}/{token}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\UsersController@specialaccess', 'as' => 'api.users.specialaccess']);
});
Route::group(['middleware' => ['isnametags']], function () {
    // Special access for nametags
    Route::post('api/householdstickers/newstickers', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@newstickers', 'as' => 'api.households.newstickers']);
    Route::post('api/householdstickers/stickers', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@stickers', 'as' => 'api.households.stickers']);
    Route::post('api/householdstickers/stickerupdate', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@stickerupdate', 'as' => 'api.households.stickerupdate']);
    Route::get('api/householdstickers/{household}/{society_id}/{accesstype}/{token}', ['uses' => 'Bishopm\Churchnet\Http\Controllers\Api\HouseholdsController@show', 'as' => 'api.households.show']);
});
