<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\PreachersRepository;
use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Repositories\PersonsRepository;
use Bishopm\Churchnet\Models\Person;
use Bishopm\Churchnet\Models\Preacher;
use Bishopm\Churchnet\Models\Plan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreatePersonRequest;
use Bishopm\Churchnet\Http\Requests\UpdatePersonRequest;

class PreachersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $preacher;
    private $person;
    private $individuals;
    private $societies;

    public function __construct(PreachersRepository $preacher, SocietiesRepository $societies, PersonsRepository $person)
    {
        $this->person = $person;
        $this->preacher = $preacher;
        $this->societies = $societies;
    }

    public function index($circuit)
    {
        return json_decode($this->person->preachers($circuit));
    }

    public function phone($circuit, Request $request)
    {
        $preacher = Preacher::with('society')->where('phone', $request->phone)->where('circuit_id', $circuit)->first();
        $yy=date('Y');
        $mm=date('n');
        $dd=date('j');
        $plans = Plan::with('society', 'service')->where('preacher_id', $preacher->id)->where('planyear', '>=', $yy)->where('planmonth', '>=', $mm)->where('planday', '>=', $dd)->orderBy('planyear', 'planmonth', 'planday')->get()->take(12);
        $data=array();
        foreach ($plans as $plan) {
            $dum['id'] = $plan->id;
            $dum['society'] = $plan->society->society;
            $dum['servicetime'] = $plan->service->servicetime;
            $dum['servicedate'] = date('Y-m-d', strtotime($plan->planmonth . "/" . $plan->planday . "/" . $plan->planyear));
            $data[$dum['servicedate']][]=$dum;
        }
        $preacher->upcoming=$data;
        return $preacher;
    }

    public function edit($circuit, Preacher $preacher)
    {
        $data['societies'] = $this->societies->all();
        $data['preacher'] = $preacher;
        return view('connexion::preachers.edit', $data);
    }

    public function create()
    {
        $data['individuals'] = $this->individuals->all();
        $data['societies'] = $this->societies->all();
        if (count($data['societies'])) {
            return view('connexion::preachers.create', $data);
        } else {
            return redirect()->route('admin.societies.create')->with('notice', 'At least one society must be added before adding a preacher');
        }
    }

    public function show($circuit, $preacher)
    {
        return $this->person->find($preacher);
    }

    public function store(CreatePersonRequest $request)
    {
        $person = $this->person->create($request->except('image', 'token', 'positions', 'fullplan', 'deletion_type', 'deletion_notes'));
        $preacher = $this->preacher->create(['person_id'=>$person->id,'fullplan'=>$request->fullplan]);
        $person->positions()->sync($request->positions);
        return $person;
    }

    public function update($circuit, Preacher $preacher, UpdatePersonRequest $request)
    {
        $person = $preacher->person;
        $this->person->update($person, $request->except('fullplan', 'positions', 'token', 'image', 'deletion_type', 'deletion_notes'));
        $person->positions()->sync($request->positions);
        $this->preacher->update($preacher, ['fullplan'=>$request->fullplan]);
    }


    public function destroy($circuit, Preacher $preacher)
    {
        $this->preacher->destroy($preacher);
    }
}
