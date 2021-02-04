<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Service;
use Bishopm\Churchnet\Models\Circuit;
use Bishopm\Churchnet\Models\Plan;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Auth;
use Bishopm\Churchnet\Http\Requests\CreateSocietyRequest;
use Bishopm\Churchnet\Http\Requests\UpdateSocietyRequest;

class SocietiesController extends ApiController
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

    public function independents()
    {
        return Society::whereNull('circuit_id')->orderBy('society')->get();
    }

    public function search(Request $request)
    {
        if (isset($request->scope)) {
            if ($request->scope == 'society') {
                return Society::with('location')->where('id', $request->entity)->get();
            } elseif ($request->scope == 'circuit') {
                return Society::with('location')->where('circuit_id', $request->entity)->orderBy('society')->get();
            } elseif ($request->scope == 'district') {
                $circs = Circuit::where('district_id', $request->entity)->select('id')->get()->toArray();
                return Society::with('location')->whereIn('circuit_id', $circs)->orderBy('society')->get();
            }
        } else {
            $circs=array();
            foreach ($request->circuits as $circ) {
                $circs[]=intval($circ);
            }
            return Society::with('location')->whereIn('circuit_id', $circs)->where('society', 'like', '%' . $request->search . '%')->orderBy('society')->get();
        }
    }

    public function settings(Request $request)
    {
        $asocs = array();
        foreach ($request->societies['keys'] as $key) {
            if ($request->societies[$key]=='admin') {
                $asocs[]=$key;
            }
        }
        return Society::whereIn('id', $asocs)->orderBy('society')->get();
    }

    public function thisweek($circuit)
    {
        $yy=date("Y", strtotime('sunday'));
        $mm=date("n", strtotime('sunday'));
        $dd=date("j", strtotime('sunday'));
        $ss=date("d M Y", strtotime('sunday'));
        $data=array();
        $societies = Society::with('services')->where('active','yes')->whereHas('services')->where('circuit_id', $circuit)->orderBy('society')->get();
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
        $data['societies'] = $societies;
        $data['sunday'] = $ss;
        return $data;
    }

    public function edit($circuit, Society $society)
    {
        return json_encode($this->society->find($society));
    }

    public function create($circuit)
    {
        return view('connexion::societies.create');
    }

    public function show($society)
    {
        return $this->society->findsociety($society);
    }

    public function appstore(Request $request)
    {
        $socnew=$request->society;
        $location=$socnew['location'];
        unset($socnew['location']);
        $soc=$this->society->create($socnew);
        $soc->location()->create([
            'address'=>$location['address'],
            'latitude'=>$location['latitude'],
            'longitude'=>$location['longitude']
        ]);
        $soc->slug = $soc->id;
        $soc->save();
        return $soc;
    }

    public function update(Request $request)
    {
        $upd = $request->society;
        $location=$upd['location'];
        unset($upd['location']);
        unset($upd['services']);
        unset($upd['users']);
        $society = Society::find($upd['id']);
        $society->location->address = $location['address'];
        $society->location->longitude = $location['longitude'];
        $society->location->latitude = $location['latitude'];
        $society->location->save();
        $society->update($upd);
        return $society;
    }

    public function useradded(Request $request)
    {
        $society = Society::create([
            'society' => $request->society,
            'circuit_id' => $request->circuit,
            'active' => 'no'
        ]);
        $society->location()->create([
            'locatable_id' => $society->id,
            'locatable_type' => 'Bishopm\Churchnet\Models\Society',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);
        $society->services()->create([
            'servicetime' => $request->servicetime,
            'language' => $request->servicelanguage,
            'society_id' => $society->id
        ]);
        return $society;
    }

    public function destroy($circuit, Society $society)
    {
        $this->society->destroy($society);
    }

    public function journeysettings($id) {
        return Society::with('circuit.district.denomination')->find($id);
    }
}
