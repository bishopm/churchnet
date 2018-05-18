<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Models\Society;
use App\Http\Controllers\Controller;
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

    public function edit($circuit, Society $society)
    {
        return json_encode($this->society->find($society));
    }

    public function create($circuit)
    {
        return view('connexion::societies.create');
    }

    public function show($circuit, $society)
    {
        return json_encode($this->society->findsociety($circuit, $society));
    }

    public function store($circuit, CreateSocietyRequest $request)
    {
        $soc=$this->society->create($request->except('token'));
        $soc->circuit_id=$circuit;
        $soc->slug = $soc->id;
        $soc->save();
        return "New society added";
    }
    
    public function update($circuit, Society $society, UpdateSocietyRequest $request)
    {
        $this->society->update($society, $request->except('token'));
        return "Society has been updated";
    }

    public function destroy($circuit, Society $society)
    {
        $this->society->destroy($society);
    }
}
