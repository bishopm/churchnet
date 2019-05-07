<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Synod;
use Bishopm\Churchnet\Models\Document;
use Bishopm\Churchnet\Models\Meeting;
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
        $synod = Synod::with('circuit')->where('district_id',$request->district)->where('synodyear',$request->synodyear)->first();
        $thisday = strtotime($synod->startdate);
        while ($thisday <= strtotime($synod->enddate)) {
            $days[]=date('Y-m-d',$thisday);
            $thisday = $thisday + 86400;
        }
        $data['synod'] = $synod;
        $cutoff = time() - 7200;
        $data['agendaitems'] = Meeting::where('meetable_id','1')->where('meetable_type','Bishopm\Churchnet\Models\Synod')->where('meetingdatetime','>',$cutoff)->orderBy('meetingdatetime')->get();
        $data['documents'] = Document::where('synod_id','1')->orderBy('title')->get();
        $data['days'] = $days;
        return $data;
    }


}
