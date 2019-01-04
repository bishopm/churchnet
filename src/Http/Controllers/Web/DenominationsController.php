<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Models\District;
use Bishopm\Churchnet\Models\Denomination;
use App\Http\Controllers\Controller;
use Mapper;

class DenominationsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
        $denominations = Denomination::orderBy('slug')->get();
        return view('churchnet::denominations.index', compact('denominations'));
	}

	public function show($slug)
	{
        $denomination=Denomination::where('slug',$slug)->with('individuals','districts','circuits.societies','location')->first();
        $first=true;
        foreach ($denomination->circuits as $circuit){
            foreach ($circuit->societies as $society){
                $title=$society->society . " (" . $society->circuit->circuitnumber . " " . $society->circuit->circuit . ")";
                if ($first){
                    Mapper::map($society->latitude, $society->longitude, ['zoom' => 4, 'center' => true, 'markers' => ['title' => $title]]);
                    $first=false;
                }
                Mapper::marker($society->latitude, $society->longitude, ['title' => $title]);
            }
        }
        if ($denomination->location) {
            Mapper::map($denomination->location->latitude, $denomination->location->longitude, ['zoom' => 15, 'center' => true, 'markers' => ['title' => $denomination->location->description]]);
            Mapper::marker($denomination->location->latitude, $denomination->location->longitude, ['title' => $denomination->location->description]);
        }
        $data['denomination']=$denomination;
        $data['title']=$denomination->denomination . " - " . str_plural($denomination->provincial);
        return view('churchnet::denominations.show', $data);
	}


}