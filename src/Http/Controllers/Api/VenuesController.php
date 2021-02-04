<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Venue;
use DB;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class VenuesController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index($society)
    {
        $data['venues']=Venue::where('society_id', $society)->orderBy('venue', 'ASC')->get();
        $data['venueusers']=DB::table('taggable_tags')->where('type','venueuser')->orderBy('name','ASC')->get();
        $bookings = DB::table('venuebookings')->join('venues', 'venuebookings.venue_id','=','venues.id')
            ->join('taggable_taggables','taggable_taggables.taggable_id','=','venuebookings.id')
            ->join('taggable_tags','taggable_taggables.tag_id','=','taggable_tags.tag_id')
            ->where('taggable_tags.type','=','venueuser')
            ->where('venues.society_id',$society)->select('venuebookings.*','venues.venue','venues.colour','taggable_tags.name')
            ->orderBy('venuebookings.starttime','ASC')->get();
        $events = array();
        $ndx = 0;
        foreach ($bookings as $index=>$booking) {
            $booking->width = 100;
            $booking->x = 0;
            $booking->duration = (strtotime($booking->endtime) - strtotime($booking->starttime)) / 60;
            $booking->date = substr($booking->starttime,0,10);
            $booking->time = substr($booking->starttime,11,5);
            if ($index == 0){
                $events[0][] = $booking;
                $previous = $booking;
            } else {
                if ($booking->starttime > $previous->endtime){
                    $block = count($events[$ndx]);
                    $col = floor(100/$block);
                    for ($i=0; $i < $block; $i++){
                        $events[$ndx][$i]->width = $col;
                        $events[$ndx][$i]->x = $col * $i;
                    }
                    $ndx++;
                }
                $events[$ndx][]=$booking;
                $previous = $booking;
            }
        }
        foreach ($events as $eventarray){
            foreach ($eventarray as $event) {
                $data['events'][]=$event;
            }
        }
        return $data;
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
        $venue = Venue::create(['society_id'=>$request->society_id, 'venue'=>$request->venue]);
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
