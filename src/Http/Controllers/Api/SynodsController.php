<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Synod;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SynodsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index(Request $request)
    {
        $synod = Synod::with('circuit','documents', 'agendaitems')->where('district_id',$request->district)->where('synodyear',$request->synodyear)->first();
        $thisday = strtotime($synod->startdate);
        while ($thisday <= strtotime($synod->enddate)) {
            $days[]=date('Y-m-d',$thisday);
            $thisday = $thisday + 86400;
        }
        $data['synod'] = $synod;
        $data['days'] = $days;
        return $data;
    }


}
