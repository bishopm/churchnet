<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\MeetingsRepository;
use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Models\Meeting;
use App\Http\Controllers\Controller;
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

    public function index($circuit)
    {
        return $this->meeting->allWithRelation('society');
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
