<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Attendance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AttendancesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index()
    {
        return Attendance::all();
    }
    
    public function show($id)
    {
        return Attendance::with('users', 'societies')->where('id', $id)->first();
    }

    public function store(Request $request)
    {
        foreach ($request->individuals as $indiv) {
            $att=Attendance::create([
                'service_id'=>$request->service_id, 
                'individual_id'=>$indiv
            ]);
        }
        return "Attendance recorded";
    }
    
    public function update($attendance, Request $request)
    {
        $cir = $this->attendance->find($attendance);
        $this->attendance->update($cir, $request->all());
        return 'Attendance has been updated';
    }

    public function destroy(Attendance $attendance)
    {
        $this->attendance->destroy($attendance);
        return view('connexion::attendances.index')->withSuccess('The ' . $attendance->attendance . ' attendance has been deleted');
    }
}
