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

    public function edit(Circuit $circuit)
    {
        return view('connexion::circuits.edit', compact('circuit'));
    }

    public function create()
    {
        return view('connexion::circuits.create');
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
        $soc=$this->circuit->create($request->all());

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
