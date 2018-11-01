<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Plan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreateSocietyRequest;
use Bishopm\Churchnet\Http\Requests\UpdateSocietyRequest;

class SocietiesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $society;

    public function __construct(SocietiesRepository $society)
    {
        $this->society = $society;
    }

    public function index($circuit)
    {
        return json_decode($this->society->allforcircuit($circuit));
    }

    public function search(Request $request)
    {
        $circs=array();
        foreach ($request->circuits as $circ) {
            $circs[]=intval($circ);
        }
        return Society::whereIn('circuit_id', $circs)->where('society', 'like', '%' . $request->search . '%')->orderBy('society')->get();
    }

    public function thisweek($circuit)
    {
        $yy=date("Y", strtotime('sunday'));
        $mm=date("n", strtotime('sunday'));
        $dd=date("j", strtotime('sunday'));
        $societies = Society::with('services')->where('circuit_id', $circuit)->get();
        foreach ($societies as $society) {
            foreach ($society->services as $service) {
                $plan = Plan::with('person.individual')->where('circuit_id', $circuit)->where('society_id', $society->id)->where('service_id', $service->id)->where('planyear', $yy)->where('planmonth', $mm)->where('planday', $dd)->first();
                if (($plan) && ($plan->person)) {
                    $service->person=$plan->person->individual;
                    $service->servicetype=$plan->servicetype;
                } else {
                    $service->person="";
                    $service->servicetype="";
                }
            }
        }
        return $societies;
    }

    public function edit($circuit, Society $society)
    {
        return json_encode($this->society->find($society));
    }

    public function create($circuit)
    {
        return view('connexion::societies.create');
    }

    public function show($circuit,$society)
    {
        return $this->society->findsociety($society);
    }

    public function store($circuit, CreateSocietyRequest $request)
    {
        $soc=$this->society->create($request->except('token'));
        $soc->circuit_id=$circuit;
        $soc->slug = $soc->id;
        $soc->save();
        return $soc;
    }

    public function appstore(CreateSocietyRequest $request)
    {
        $soc=$this->society->create($request->all());
        $soc->slug = $soc->id;
        $soc->save();
        return $soc;
    }
    
    public function update(Request $request)
    {
        $upd = $request->society;
        unset($upd['services']);
        $society = Society::find($upd['id']);
        $society->update($upd);
        return $society;
    }

    public function destroy($circuit, Society $society)
    {
        $this->society->destroy($society);
    }
}
