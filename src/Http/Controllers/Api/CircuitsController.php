<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\CircuitsRepository;
use Bishopm\Churchnet\Models\Circuit;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreateCircuitRequest;
use Bishopm\Churchnet\Http\Requests\UpdateCircuitRequest;

class CircuitsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $circuit;

    public function __construct(CircuitsRepository $circuit)
    {
        $this->circuit = $circuit;
    }

    public function index()
    {
        return Circuit::orderBy('circuitnumber')->get();
    }

    public function query($circuit, Request $request)
    {
        return DB::select(DB::raw($request->sql))->toArray();
    }

    public function create()
    {
        return view('connexion::circuits.create');
    }

    public function showwithmap($id)
    {
        $data['circuit'] = Circuit::with('users', 'societies', 'ministers')->where('id', $id)->first();
        $first = true;
        foreach ($data['circuit']->societies as $society) {
            if ($society->location) {
                if ($first) {
                    $data['bounds']['minlat'] = floatval($society->location->latitude);
                    $data['bounds']['maxlat'] = floatval($society->location->latitude);
                    $data['bounds']['minlng'] = floatval($society->location->longitude);
                    $data['bounds']['maxlng'] = floatval($society->location->longitude);
                }
                $first = false;
                $title['society'] = $society;
                $title['circuit'] = $society->circuit;
                $data['markers'][] = ['title' => $title, 'lat' => $society->location->latitude, 'lng' => $society->location->longitude];
                if (floatval($society->location->latitude) < $data['bounds']['minlat']) {
                    $data['bounds']['minlat'] = floatval($society->location->latitude);
                }
                if (floatval($society->location->latitude) > $data['bounds']['maxlat']) {
                    $data['bounds']['maxlat'] = floatval($society->location->latitude);
                }
                if (floatval($society->location->longitude) < $data['bounds']['minlng']) {
                    $data['bounds']['minlng'] = floatval($society->location->longitude);
                }
                if (floatval($society->location->longitude) > $data['bounds']['maxlng']) {
                    $data['bounds']['maxlng'] = floatval($society->location->longitude);
                }
            }
        }
        foreach ($data['circuit']->ministers as $minister) {
            $dumtags = array();
            foreach ($minister->tags as $tag) {
                $dumtags[] = $tag->name;
            }
            $data['ministers'][$minister->individual->surname . $minister->individual->firstname] = $minister->individual->title . " " . $minister->individual->firstname . " " . $minister->individual->surname . " (" . implode(", ", $dumtags) . ")";
        }
        ksort($data['ministers']);
        return $data;
    }

    public function show($id)
    {
        return Circuit::with('users', 'societies')->where('id', $id)->first();
    }

    public function withsocieties($id)
    {
        return $this->circuit->withsocieties($id);
    }

    public function store(CreateCircuitRequest $request)
    {
        $soc = $this->circuit->create($request->all());

        return redirect()->route('admin.circuits.show', $soc->id)
            ->withSuccess('New circuit added');
    }

    public function update($circuit, Request $request)
    {
        $cir = $this->circuit->find($circuit);
        $this->circuit->update($cir, $request->all());
        return 'Circuit has been updated';
    }

    public function destroy(Circuit $circuit)
    {
        $this->circuit->destroy($circuit);
        return view('connexion::circuits.index')->withSuccess('The ' . $circuit->circuit . ' circuit has been deleted');
    }

    public function search(Request $request)
    {
        return Circuit::whereIn('district_id', $request->districts)->where('circuit', 'like', '%' . $request->search . '%')->orderBy('circuitnumber')->get();
    }
}
