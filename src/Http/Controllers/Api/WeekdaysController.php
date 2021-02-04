<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Weekday;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class WeekdaysController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $weekday;

    public function index(Request $request)
    {
        return Weekday::where('circuit_id', $request->circuit)->orderBy('servicedate', 'DESC')->get();
    }

    public function create()
    {
        $societies = $this->societies->all();
        return view('connexion::weekdays.create', compact('societies'));
    }

    public function show($circuit, $weekday)
    {
        return $this->weekday->findfordate($circuit, $weekday);
    }

    public function edit($weekday)
    {
        $wk=Weekday::find($weekday);
        $wk->datestr = date('Y-m-d H:i', $wk->servicedate);
        return $wk;
    }

    public function store(Request $request)
    {
        $request->merge(array('servicedate' => strtotime(substr($request->servicedate, 0, 10))));
        $wk = Weekday::create($request->all());
        return "New weekday added";
    }

    public function update($id, Request $request)
    {
        $wkday = Weekday::find($id);
        $request->merge(array('servicedate' => strtotime(substr($request->servicedate, 0, 10))));
        $wkday->update($request->all());
        return "Weekday has been updated";
    }

    public function destroy($id)
    {
        $wk=Weekday::find($id);
        $wk->delete();
        return "Weekday has been deleted";
    }
}
