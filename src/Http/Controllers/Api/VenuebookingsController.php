<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Venuebooking;
use Bishopm\Churchnet\Models\Tagg;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use DB;

class VenuebookingsController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index($id)
    {
        return Venuebooking::with('venue')->where('venue_id', $id)->get();
    }

    public function search(Request $request)
    {
        $socs=array();
        return Venuebooking::where('society_id', $request->society)->where('venuebooking', 'like', '%' . $request->search . '%')->orderBy('venuebooking','ASC')->get();
    }

    public function edit($id)
    {
        return Venuebooking::find($id);
    }

    public function store(Request $request)
    {
        $venuebooking = Venuebooking::create([
            'venue_id'=>$request->venue_id,
            'description'=>$request->description,
            'starttime'=>$request->starttime,
            'endtime'=>$request->endtime,
            'status'=>$request->status
        ]);
        $venuebooking->tag($request->venueuser);
        DB::table('taggable_tags')->where('name', $request->venueuser)->update(['type' => 'venueuser']);
        return $venuebooking;
    }

    public function update($id, Request $request)
    {
        $venuebooking = Venuebooking::find($id);
        $venuebooking->update($request->all());
        return "Venue booking has been updated";
    }

    public function destroy($id)
    {
        $venuebooking=Venuebooking::find($id);
        $venuebooking->delete();
        return "Venuebooking has been deleted";
    }
}
