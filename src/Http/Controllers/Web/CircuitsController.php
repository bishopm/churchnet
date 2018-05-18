<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\CircuitsRepository;
use Bishopm\Churchnet\Repositories\PlansRepository;
use Bishopm\Churchnet\Models\Circuit;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateCircuitRequest;
use Bishopm\Churchnet\Http\Requests\UpdateCircuitRequest;
use Mapper;
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

    public function edit(Circuit $circuit)
    {
        $districts=$this->districts->all();
        return view('churchnet::circuits.edit', compact('circuit', 'districts'));
    }

    public function create()
    {
        $districts=$this->districts->all();
        return view('churchnet::circuits.create', compact('districts'));
    }

    public function show($circuitnum)
    {
        $data['circuit']=Circuit::with('societies')->where('slug', $circuitnum)->first();
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
        $preachers=$this->preachers->allforcircuit($data['circuit']->id);
        foreach ($preachers as $preacher) {
            if (($preacher->status=="Local preacher") or ($preacher->status=="Emeritus preacher") or ($preacher->status=="On trial preacher")) {
                $thisp=$preacher->title . " " . substr($preacher->firstname, 0, 1) . " " . $preacher->surname;
                if ($preacher->status=="Emeritus preacher") {
                    $thisp.=' [Emeritus]';
                } elseif ($preacher->status=="On trial preacher") {
                    $thisp.=' [Trial]';
                }
                $data['preachers'][]=$thisp . " (" . $preacher->society->society . ")";
            } else {
                $data[str_replace(' ', '_', $preacher->status)][]=$preacher;
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
