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
        Return Synod::with('circuit')->where('district_id',$request->district)->where('synodyear',$request->synodyear)->first();
    }


}
