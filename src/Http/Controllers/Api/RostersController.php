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
        $rosters=Roster::where('society_id', $request->society)->get();
        return $rosters;
    }

    public function show($id, $yr, $mth)
    {
        $roster = Roster::with('rostergroups.group', 'rostergroups.rosteritems.individual', 'society')->find($id);
        $firstweek = "First " . $roster->dayofweek . " of " . $mth . " " . $yr;
        $weeks[0] = date("Y-m-d", strtotime($firstweek));
        $weeks[] = date("Y-m-d", strtotime($weeks[0] . " + 1 week"));
        $weeks[] = date("Y-m-d", strtotime($weeks[0] . " + 2 weeks"));
        $weeks[] = date("Y-m-d", strtotime($weeks[0] . " + 3 weeks"));
        if ((date("n", strtotime($weeks[0]))) == (date("n", strtotime($weeks[0] . " + 4 weeks")))) {
            $weeks[] = date("Y-m-d", strtotime($weeks[0] . " + 4 weeks"));
        }
        $data['roster'] = $roster;
        foreach ($roster->rostergroups as $rg) {
            $row = new \stdClass;
            $row->groups = new \stdClass;
            $row->groups->label = $rg->group->groupname;
            $row->groups->id = $rg->group->id;
            foreach ($weeks as $kk=>$wk) {
                $row->$kk = new \stdClass;
                $row->$kk->label='';
                $row->$kk->id='';
            }
            foreach ($rg->rosteritems as $ri) {
                $wk = array_search($ri->rosterdate, $weeks);
                if ($wk) {
                    $row->$wk->label=$ri->individual->firstname . " " . $ri->individual->surname;
                    $row->$wk->id=$ri->individual_id;
                }
            }
            $data['rows'][]=$row;
        }
        $firstcol = new \stdClass;
        $firstcol->name = "groups";
        $firstcol->field = "groups";
        $firstcol->label = "Groups";
        $firstcol->align = "left";
        $data['columns'][]=$firstcol;
        foreach ($weeks as $kk=>$ww) {
            $col = new \stdClass;
            $col->name = $kk;
            $col->field = $kk;
            $col->label = $ww;
            $col->align = 'center';
            $data['columns'][]=$col;
        }
        return json_encode($data);
    }

    public function edit($id)
    {
        return Roster::with('rostergroups')->where('id', $id)->first();
    }

    public function store(Request $request)
    {
        $roster = Roster::create(['name' => $request->name, 'dayofweek' => $request->dayofweek, 'society_id' => $request->society_id]);
        return $roster;
    }
}
