<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\CircuitsRepository;
use Bishopm\Churchnet\Models\Circuit;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreateCircuitRequest;
use Bishopm\Churchnet\Http\Requests\UpdateCircuitRequest;

class CircuitsController extends Controller {

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
        $circuits = $this->circuit->all();
        return $circuits;
    }
    
    public function query($circuit, Request $request)
    {
        return DB::select( DB::raw($request->sql))->toArray();
    }

	public function edit(Circuit $circuit)
    {
        return view('connexion::circuits.edit', compact('circuit'));
    }

    public function create()
    {
        return view('connexion::circuits.create');
    }

	public function show($no)
	{
        return $this->circuit->find($no);
	}

    public function store(CreateCircuitRequest $request)
    {
        $soc=$this->circuit->create($request->all());

        return redirect()->route('admin.circuits.show',$soc->id)
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
        return view('connexion::circuits.index')->withSuccess('The ' . $circuit->circuit . ' circuit has been deleted');
    }

}