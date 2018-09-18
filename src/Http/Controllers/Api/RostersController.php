<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Roster;
use Bishopm\Churchnet\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RostersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index(Request $request)
    {
        $rosters=Roster::where('society_id',$request->society)->get();
        return $rosters;
    }

    public function show($id, $yr, $mth)
    {
        $roster = Roster::with('rostergroups.group','rostergroups.rosteritems.individual','society')->find($id);
        $weeks[0] = date("Y-m-d", strtotime("First " . $roster->dayofweek . " of " . $mth . " " . $yr));
        $weeks[] = date("Y-m-d", strtotime($weeks[0] . " + 1 week"));
        $weeks[] = date("Y-m-d", strtotime($weeks[0] . " + 2 weeks"));
        $weeks[] = date("Y-m-d", strtotime($weeks[0] . " + 3 weeks"));
        if ((date("n", strtotime($weeks[0]))) == (date("n", strtotime($weeks[0] . " + 4 weeks")))) {
            $weeks[] = date("Y-m-d", strtotime($weeks[0] . " + 4 weeks"));
        }
        $data['roster'] = $roster;
        foreach ($roster->rostergroups as $rg) {
            $row = new \stdClass;
            $row->name = $rg->group->groupname;
            foreach ($weeks as $kk=>$wk) {
                $row->$wk='';
            }
            foreach ($rg->rosteritems as $ri) {
                $wk = $ri->rosterdate;
                $row->$wk = $ri->individual->firstname . ' ' . $ri->individual->surname;
            }
            $data['rows'][]=$row;
        }
        $firstcol = new \stdClass;
        $firstcol->field = "name";
        $firstcol->label = "Groups";
        $firstcol->align = "left";
        $data['columns'][]=$firstcol;
        foreach ($weeks as $ww){
            $col = new \stdClass;
            $col->label = $ww;
            $col->align = 'left';
            $data['columns'][]=$col;
        }
        return $data;
    }

}
