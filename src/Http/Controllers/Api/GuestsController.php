<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Guest;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GuestsController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index(Request $request){
        $this->search=$request->search;
        return Guest::with('person.individual')->where('circuit_id',$request->circuit)
        ->whereHas('person.individual', function ($q) {
            $q->where('surname','like','%' . $this->search . '%')->orWhere('firstname','like','%' . $this->search . '%');
        })->get();
    }

    public function store(Request $request){
        $indiv = Individual::with('person')->where('id',$request->id)->first();
        return Guest::create([
            'circuit_id' => $request->circuit,
            'person_id' => $indiv->person->id
        ]);
    }
}
