<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\MeetingsRepository;
use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Models\Meeting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreateMeetingRequest;
use Bishopm\Churchnet\Http\Requests\UpdateMeetingRequest;

class MeetingsController extends Controller
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
        return Meeting::with('society')->where('circuit_id', $request->circuit)->orderBy('meetingdatetime','DESC')->get();
    }

    public function upcoming($circuit)
    {
        $now = time();
        $upcomings = Meeting::with('society')->where('meetingdatetime', '>', $now)->where('circuit_id', $circuit)->orderBy('meetingdatetime')->get();
        $data = array();
        foreach ($upcomings as $upcoming) {
            $dum['start'] = date("j F Y (H:i)", $upcoming->meetingdatetime);
            $dum['details'] = $upcoming->description;
            $dum['society'] = $upcoming->society->society;
            $dum['society_id'] = $upcoming->society->id;
            $data[]=$dum;
        }
        return $data;
    }

    public function edit($circuit, $meeting)
    {
        return $this->meeting->find($meeting);
    }

    public function show(Meeting $meeting)
    {
        $data['meeting']=$meeting;
        return view('connexion::meetings.show', $data);
    }

    public function store($circuit, CreateMeetingRequest $request)
    {
        $data=$request->except('token');
        $data['circuit_id']=$circuit;
        $this->meeting->create($data);
        return "New meeting added";
    }
    
    public function update($circuit, Meeting $meeting, UpdateMeetingRequest $request)
    {
        $data=$request->except('token');
        $this->meeting->update($meeting, $data);
        return "Meeting has been updated";
    }

    public function destroy($circuit, Meeting $meeting)
    {
        $this->meeting->destroy($meeting);
    }
}
