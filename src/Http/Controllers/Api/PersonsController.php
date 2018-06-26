<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\PeopleRepository;
use Bishopm\Churchnet\Repositories\PositionsRepository;
use Bishopm\Churchnet\Models\Person;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreatePersonRequest;
use Bishopm\Churchnet\Http\Requests\UpdatePersonRequest;
use DB;

class PeopleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $person;
    private $positions;

    public function __construct(PeopleRepository $person, PositionsRepository $positions)
    {
        $this->person = $person;
        $this->positions = $positions;
    }

    public function index($circuit)
    {
        $people=DB::select(DB::raw("SELECT people.id FROM people LEFT JOIN preachers ON preachers.person_id = people.id WHERE preachers.id is null"));
        $ids=array();
        foreach ($people as $person) {
            $ids[]=$person->id;
        }
        $data=Person::with('positions')->whereIn('id', $ids)->get();
        return $data;
    }

    public function phone($circuit, Request $request)
    {
        $person = Person::with('society')->where('phone', $request->phone)->where('circuit_id', $circuit)->first();
        $yy=date('Y');
        $mm=date('n');
        $dd=date('j');
        $plans = Plan::with('society', 'service')->where('person_id', $person->id)->where('planyear', '>=', $yy)->where('planmonth', '>=', $mm)->where('planday', '>=', $dd)->orderBy('planyear', 'planmonth', 'planday')->get()->take(12);
        foreach ($plans as $plan) {
            $dum['id'] = $plan->id;
            $dum['society'] = $plan->society->society;
            $dum['servicetime'] = $plan->service->servicetime;
            $dum['servicedate'] = date('Y-m-d', strtotime($plan->planmonth . "/" . $plan->planday . "/" . $plan->planyear));
            $data[$dum['servicedate']][]=$dum;
        }
        $person->upcoming=$data;
        return $person;
    }

    public function edit($circuit, Person $person)
    {
        $data['societies'] = $this->societies->all();
        $data['person'] = $person;
        return view('connexion::people.edit', $data);
    }

    public function create()
    {
        $data['individuals'] = $this->individuals->all();
        $data['societies'] = $this->societies->all();
        if (count($data['societies'])) {
            return view('connexion::people.create', $data);
        } else {
            return redirect()->route('admin.societies.create')->with('notice', 'At least one society must be added before adding a person');
        }
    }

    public function show($circuit, $person)
    {
        $data['person']=$this->person->find($person);
        $data['positions']=$this->positions->all();
        return $data;
    }

    public function store(CreatePersonRequest $request)
    {
        $person = $this->person->create($request->except('image', 'token', 'positions'));
        $person->positions()->sync($request->positions);
        return $person;
    }
    
    public function update($circuit, Person $person, UpdatePersonRequest $request)
    {
        $this->person->update($person, $request->except('token', 'positions'));
        $person->positions()->sync($request->positions);
        return $person;
    }

    public function destroy($circuit, Person $person)
    {
        $this->person->destroy($person);
    }
}
