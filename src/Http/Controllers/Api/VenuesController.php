<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Venue;
use Bishopm\Churchnet\Models\Society;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VenuesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index($society)
    {
        return Venue::where('society_id', $society)->orderBy('venue', 'ASC')->get();
    }

    public function search(Request $request)
    {
        $socs=array();
        return Venue::where('society_id', $request->society)->where('venue', 'like', '%' . $request->search . '%')->orderBy('venue','ASC')->get();
    }

    public function edit($id)
    {
        return Venue::find($id);
    }

    public function store(Request $request)
    {
        $venue = Venue::create(['society_id'=>$request->society_id, 'venuedate'=>substr($request->venuedate, 0, 10), 'pgnumber'=>$request->pgnumber, 'amount'=>$request->amount]);
        return "New venue added";
    }
    
    public function update($id, Request $request)
    {
        $venue = Venue::find($id);
        $venue->update($request->all());
        return "Venue has been updated";
    }

    public function destroy($id)
    {
        $venue=Venue::find($id);
        $venue->delete();
        return "Venue has been deleted";
    }
}
