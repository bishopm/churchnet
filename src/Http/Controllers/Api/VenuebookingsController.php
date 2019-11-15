<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Venuebooking;
use Bishopm\Churchnet\Models\Society;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VenuebookingsController extends Controller
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
        $venuebooking = Venuebooking::create(['society_id'=>$request->society_id, 'venuebooking'=>$request->venuebooking]);
        return "New venuebooking added";
    }
    
    public function update($id, Request $request)
    {
        $venuebooking = Venuebooking::find($id);
        $venuebooking->update($request->all());
        return "Venuebooking has been updated";
    }

    public function destroy($id)
    {
        $venuebooking=Venuebooking::find($id);
        $venuebooking->delete();
        return "Venuebooking has been deleted";
    }
}
