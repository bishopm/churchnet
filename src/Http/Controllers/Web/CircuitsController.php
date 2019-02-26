<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\CircuitsRepository;
use Bishopm\Churchnet\Repositories\PlansRepository;
use Bishopm\Churchnet\Models\Circuit;
use Bishopm\Churchnet\Models\Person;
use Bishopm\Churchnet\Models\Individual;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateCircuitRequest;
use Bishopm\Churchnet\Http\Requests\UpdateCircuitRequest;
use Auth;
use Bishopm\Churchnet\Repositories\PreachersRepository;
use Bishopm\Churchnet\Repositories\DistrictsRepository;

class CircuitsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $circuit;
    private $plans;
    private $preachers;
    private $districts;

    public function __construct(CircuitsRepository $circuit, PlansRepository $plans, PreachersRepository $preachers, DistrictsRepository $districts)
    {
        $this->circuit = $circuit;
        $this->plans = $plans;
        $this->preachers = $preachers;
        $this->districts = $districts;
    }

    public function index()
    {
        $circuits = $this->circuit->all();
        return view('churchnet::circuits.index', compact('circuits'));
    }

    public function my()
    {
        $user = Auth::user();
        $circuit = Circuit::find($user->circuit_id);
        $districts=$this->districts->all();
        $leaders = $circuit->people;
        return view('churchnet::circuits.edit', compact('circuit', 'districts', 'leaders', 'positions'));
    }

    public function edit(Circuit $circuit)
    {
        $districts=$this->districts->all();
        $leaders = $circuit->people;
        return view('churchnet::circuits.edit', compact('circuit', 'districts', 'leaders', 'positions'));
    }

    public function create()
    {
        $districts=$this->districts->all();
        return view('churchnet::circuits.create', compact('districts'));
    }

    public function show($circuitnum)
    {
        $data['circuit']=Circuit::with('societies', 'people.tags', 'district.denomination')->where('slug', $circuitnum)->first();
        $first=true;
        $socs=array();
        foreach ($data['circuit']->societies as $society) {
            $title="<b><a href=\"" . url('/circuits/' . $data['circuit']->slug . '/' . $society->slug) . "\">" . $society->society . "</a></b>";
            $title=str_replace('\'', '\\\'', $title);
            $data['markers'][]=['title'=>$title, 'lat'=>$society->latitude, 'lng'=>$society->longitude];
            $socs[]=$society->id;
        }
        $data['plan']=count($this->plans->latestplan($data['circuit']->id));
        $data['preachers'] = $this->circuit->preachers($data['circuit']->id);
        $super = $data['circuit']->tagged('superintendent')->first();
        $ministers = $data['circuit']->tagged('circuit minister')->get();
        $data['ministers'] = array();
        foreach ($ministers as $min) {
            if ($min->id == $super->id) {
                $min->supt = " (supt)";
            } else {
                $min->supt = "";
            }
            $data['ministers'][$min->individual->surname . $min->individual->firstname]=$min;
        }
        if (isset($data['ministers'])) {
            ksort($data['ministers']);
        }
        $data['supernumeraries'] = $data['circuit']->tagged('supernumerary minister')->get();
        $data['stewards'] = Individual::societymember($socs)->withAnyTags('circuit steward')->get();
        return view('churchnet::circuits.show', $data);
    }

    public function store(CreateCircuitRequest $request)
    {
        $cir=$this->circuit->create($request->all());
        $cir->slug=str_slug($request->circuit);
        $chk=$this->circuit->findBySlug($cir->slug);
        if (count($chk)) {
            $cir->slug=$cir->id;
        }
        $cir->save();
        return redirect()->route('admin.circuits.index')
            ->withSuccess('New circuit added');
    }
    
    public function update(Circuit $circuit, UpdateCircuitRequest $request)
    {
        $this->circuit->update($circuit, $request->all());
        return redirect()->route('admin.circuits.index')->withSuccess('Circuit has been updated');
    }

    public function destroy(Circuit $circuit)
    {
        $this->circuit->destroy($circuit);
        return view('churchnet::circuits.index')->withSuccess('The ' . $circuit->circuit . ' circuit has been deleted');
    }
}
