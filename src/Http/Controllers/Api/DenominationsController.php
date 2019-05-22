<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Denomination;
use App\Http\Controllers\Controller;

class DenominationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function societies($id)
    {
        $societies = Denomination::find($id)->with('circuits.societies')->first();
        $socs = array();
        foreach ($societies->circuits as $circuit) {
            foreach ($circuit->societies as $society) {
                $key = strtoupper($society->society) . '_' . $society->id; 
                $socs[$key]['society'] = $society->society;
                $socs[$key]['society_id'] = $society->id;
                $socs[$key]['circuit'] = $society->circuit->circuit;
                $socs[$key]['circuit_id'] = $society->circuit->id;
                $socs[$key]['district'] = $society->circuit->district->district;
                $socs[$key]['district_id'] = $society->circuit->district->id;
            }
        }
        return $socs;
    }

}
