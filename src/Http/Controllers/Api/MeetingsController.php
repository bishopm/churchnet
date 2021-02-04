<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\MeetingsRepository;
use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Models\Meeting;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Circuit;
use Bishopm\Churchnet\Models\District;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class MeetingsController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $meeting;
    private $societies;

    public function __construct(MeetingsRepository $meeting, SocietiesRepository $societies)
    {
        $this->meeting = $meeting;
        $this->societies = $societies;
    }

    public function index(Request $request)
    {
        $mtype = "Bishopm\\Churchnet\\Models\\" . ucfirst($request->scope);
        $data = array();
        $now=time();
        $data['meetings']=Meeting::with('society')->where('meetingdatetime','>',$now)->where('meetable_id', $request->id)->where('meetable_type', $mtype)->orderBy('meetingdatetime', 'ASC')->get();
        if ($request->scope == 'society') {
            $data['entity']=Society::find($request->id);
        } elseif ($request->scope == 'circuit') {
            $data['entity']=Circuit::find($request->id);
        } elseif ($request->scope == 'district') {
            $data['entity']=District::find($request->id);
        }
        return $data;
    }

    public function upcoming($scope, $soc)
    {
        $now = time();
        $data = array();
        $society = Society::with('circuit.district')->find($soc);
        if ($scope == 'Society') {
            $upcomings = Meeting::with('society')->where('meetingdatetime', '>', $now)->where('meetable_type', 'Bishopm\Churchnet\Models\Society')->where('meetable_id', $society->id)->orderBy('meetingdatetime')->get();
            $data['entity'] = $society->society;
        } elseif ($scope == 'Circuit') {
            $upcomings = Meeting::with('society')->where('meetingdatetime', '>', $now)->where('meetable_type', 'Bishopm\Churchnet\Models\Circuit')->where('meetable_id', $society->circuit->id)->orderBy('meetingdatetime')->get();
            $data['entity'] = $society->circuit->circuit;
        } elseif ($scope == 'District') {
            $upcomings = Meeting::with('society')->where('meetingdatetime', '>', $now)->where('meetable_type', 'Bishopm\Churchnet\Models\District')->where('meetable_id', $society->circuit->district->id)->orderBy('meetingdatetime')->get();
            $data['entity'] = $society->circuit->district->district . " District";
        }
        foreach ($upcomings as $upcoming) {
            $dum['start'] = $upcoming->meetingdatetime;
            $dum['details'] = $upcoming->description;
            $dum['society'] = $upcoming->society->society;
            $dum['society_id'] = $upcoming->society->id;
            $data['events'][]=$dum;
        }
        return $data;
    }

    public function edit($meeting)
    {
        $mtg = $this->meeting->find($meeting);
        $mtg->datestr = date('Y-m-d H:i', $mtg->meetingdatetime);
        return $mtg;
    }

    public function show(Meeting $meeting)
    {
        $data['meeting']=$meeting;
        return view('connexion::meetings.show', $data);
    }

    public function store(Request $request)
    {
        $request->merge(array('meetingdatetime' => strtotime(substr($request->meetingdatetime, 0, 19))));
        $request->merge(array('meetable_type' => 'Bishopm\\Churchnet\\Models\\' . ucfirst($request->meetable_type)));
        $this->meeting->create($request->all());
        return "New meeting added";
    }

    public function storeagendaitems(Request $request)
    {
        $meeting = Meeting::create([
            'meetable_id' => 1,
            'meetable_type' => 'Bishopm\Churchnet\Models\Synod',
            'description' => $request->agenda['description'],
            'meetingdatetime' => strtotime($request->agenda['agendadate'] . ' ' . $request->agenda['agendatime'] . ':00'),
            'preachingplan' => 'no'
        ]);
        return $meeting;
    }

    public function update($id, Request $request)
    {
        $meeting = Meeting::find($id);
        $request->merge(array('meetingdatetime' => strtotime(substr($request->meetingdatetime, 0, 19))));
        $request->merge(array('meetable_type' => 'Bishopm\\Churchnet\\Models\\' . ucfirst($request->meetable_type)));
        $meeting->update($request->all());
        return "Meeting has been updated";
    }

    public function destroy($id)
    {
        $mtg=Meeting::find($id);
        $this->meeting->destroy($mtg);
        return "Meeting has been deleted";
    }
}
