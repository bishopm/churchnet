<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Roster;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Rosteritem;
use Bishopm\Churchnet\Models\Rostergroup;
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
        $roster = Roster::with('rostergroups.group', 'rostergroups.rosteritems', 'society')->where('id', $id)->first();
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
            $row->groups->rostergroup_id = $rg->id;
            $row->groups->maxpeople = $rg->maxpeople;
            foreach ($weeks as $kk=>$wk) {
                $row->$kk = new \stdClass;
                $row->$kk->people=[];
            }
            foreach ($rg->rosteritems as $ri) {
                $wk = array_search($ri->rosterdate, $weeks);
                if (($wk) or ($wk === 0)) {
                    $indivs = explode(',',$ri->individuals);
                    $people=array();
                    foreach ($indivs as $indiv) {
                        $person = Individual::find($indiv);
                        $dum['label']=substr($person->firstname, 0, 1) . " " . $person->surname;
                        $dum['id']=$person->id;
                        $people[]=$dum;
                    }
                    $row->$wk->people=$people;
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
        return Roster::with('rostergroups.group', 'society')->where('id', $id)->first();
    }

    public function store(Request $request)
    {
        $roster = Roster::create(['name' => $request->name, 'dayofweek' => $request->dayofweek, 'society_id' => $request->society_id]);
        return $roster;
    }

    public function storeitem(Request $request)
    {
        $delete = Rosteritem::where('rostergroup_id', $request->rostergroup_id)->where('rosterdate', $request->rosterdate)->first();
        if ($delete) {
            $delete->delete();
        }
        if ($request->individualarray) {
            $rosteritem = Rosteritem::create(['rostergroup_id' => $request->rostergroup_id, 'rosterdate' => $request->rosterdate, 'individuals' => implode(",",$request->individualarray)]);
        } else {
            $rosteritem = Rosteritem::create(['rostergroup_id' => $request->rostergroup_id, 'rosterdate' => $request->rosterdate, 'individuals' => $request->individual_id]);
        }       
        return $rosteritem;
    }

    public function storerostergroup(Request $request)
    {
        $rostergroup = Rostergroup::create(['group_id' => $request->group_id, 'roster_id' => $request->roster_id, 'maxpeople' => $request->maxpeople]);
        return Rostergroup::with('group')->where('id',$rostergroup->id)->first();
    }

    public function deleterostergroup($id)
    {
        $rostergroup = Rostergroup::find($id);
        $rostergroup->delete();
        return "Roster group deleted";
    }
}
