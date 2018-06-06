<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\DistrictsRepository;
use Bishopm\Churchnet\Models\District;
use Bishopm\Churchnet\Models\Circuit;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DistrictsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $district;

    public function __construct(DistrictsRepository $district)
    {
        $this->district = $district;
    }

    public function index()
    {
        return District::orderBy('district')->get();
    }

    public function show($id)
    {
        return Circuit::where('district_id', $id)->orderBy('circuitnumber')->get();
    }
}
