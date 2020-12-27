<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\PeopleRepository;
use Bishopm\Churchnet\Repositories\TagsRepository;
use Bishopm\Churchnet\Models\Person;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Circuit;
use Bishopm\Churchnet\Models\Individual;
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
    private $tags;

    public function __construct(PeopleRepository $person, TagsRepository $tags)
    {
        $this->person = $person;
        $this->tags = $tags;
    }

    public function index($circuit)
    {
        $people=$this->people->all();
        return compact('people');
    }

    public function search(Request $request)
    {
        $circs=array();
        foreach ($request->circuits as $circ) {
            $circs[]=intval($circ);
        }
        $societies = Society::whereIn('circuit_id', $circs)->pluck('id')->toArray();
        $data['people'] = Individual::societymember($societies)->whereHas('person')->with('person.tags')->where('surname', 'like', '%' . $request->search . '%')->orderBy('surname')->orderBy('firstname')->get();
        return $data;
    }

    public function guestsearch(Request $request)
    {
        $mycircuit=Circuit::where('id',$request->circuit)->first();
        $circuits=Circuit::where('district_id',$mycircuit->district_id)->where('id','<>',$request->circuit)->get();
        $circs=array();
        foreach ($circuits as $circ) {
            $circs[]=intval($circ->id);
        }
        $societies = Society::whereIn('circuit_id', $circs)->pluck('id')->toArray();
        $data['people'] = Individual::societymember($societies)->whereHas('person')->with('person.tags')->where('surname', 'like', '%' . $request->search . '%')->orderBy('surname')->orderBy('firstname')->get();
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

    public function show($person)
    {
        $data['person']=$this->person->find($person);
        return $data;
    }

    public function appshow($person)
    {
        $person = Person::with('tags', 'individual', 'individual.household.society')->where('id', $person)->first();
        $person->alltags = $this->tags->all();
        return $person;
    }

    public function store(Request $request)
    {
        $person = $this->person->create($request->except('roles'));
        $person->detag();
        foreach ($request->roles as $role) {
            $tag = $this->tags->find($role);
            $person->tag($tag->name);
        }
        return $person;
    }
    
    public function update($circuit, $id, Request $request)
    {
        $person = $this->person->find($id);
        $person->update($request->except('roles'));
        $person->detag();
        foreach ($request->roles as $role) {
            $tag = $this->tags->find($role);
            $person->tag($tag->name);
        }
        return $person;
    }

    public function destroy($id)
    {
        $person=Person::find($id);
        $person->detag();
        $person->delete();
        return "Person has been deleted";
    }
}
