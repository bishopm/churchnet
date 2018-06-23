<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\CircuitsRepository;
use Bishopm\Churchnet\Repositories\PlansRepository;
use Bishopm\Churchnet\Models\Circuit;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateCircuitRequest;
use Bishopm\Churchnet\Http\Requests\UpdateCircuitRequest;
use Mapper;
use Auth;
use Bishopm\Churchnet\Repositories\PreachersRepository;
use Bishopm\Churchnet\Repositories\SettingsRepository;
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
    private $settings;
    private $districts;

    public function __construct(CircuitsRepository $circuit, PlansRepository $plans, PreachersRepository $preachers, SettingsRepository $settings, DistrictsRepository $districts)
    {
        $this->circuit = $circuit;
        $this->plans = $plans;
        $this->preachers = $preachers;
        $this->settings = $settings;
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
        $leaders = $circuit->persons;
        return view('churchnet::circuits.edit', compact('circuit', 'districts', 'leaders', 'positions'));
    }

    public function edit(Circuit $circuit)
    {
        $districts=$this->districts->all();
        $leaders = $circuit->persons;
        return view('churchnet::circuits.edit', compact('circuit', 'districts', 'leaders', 'positions'));
    }

    public function create()
    {
        $districts=$this->districts->all();
        return view('churchnet::circuits.create', compact('districts'));
    }

    public function show($circuitnum)
    {
        $data['circuit']=Circuit::with('societies', 'persons.preacher', 'persons.positions', 'persons.society')->where('slug', $circuitnum)->first();
        $settings=$this->settings->allforcircuit($data['circuit']->id);
        foreach ($settings as $setting) {
            $data['settings'][$setting->setting_key]=$setting->setting_value;
        }
        $first=true;
        foreach ($data['circuit']->societies as $society) {
            if ($first) {
                Mapper::map($society->latitude, $society->longitude, ['cluster' => false, 'marker' => false, 'type' => 'HYBRID', 'center'=>false]);
                $first=false;
            }
            $info="go to <a href=\"" . url('/') . "/" . $data['circuit']->slug . "/" . $society->slug . "\">" . $society->society . "</a>";
            Mapper::informationWindow($society->latitude, $society->longitude, $info, ['title' => $society->society]);
        }
        $data['plan']=count($this->plans->latestplan($data['circuit']->id));
        $data['preachers'] = array();
        $persons=$data['circuit']->persons;
        foreach ($persons as $person) {
            foreach ($person->positions as $position) {
                if (($position->position=="Local preacher") or ($position->position=="Emeritus preacher") or ($position->position=="On trial preacher")) {
                    $thisp=$person->title . " " . substr($person->firstname, 0, 1) . " " . $person->surname;
                    if ($position->position=="Emeritus preacher") {
                        $thisp.=' [Emeritus]';
                    } elseif ($position->position=="On trial preacher") {
                        $thisp.=' [Trial]';
                    }
                    $data['preachers'][]=$thisp . " (" . $person->society->society . ")";
                } else {
                    $data[str_replace(' ', '_', $position->position)][]=$person;
                }
            }
        }
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
