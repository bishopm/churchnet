<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Roster;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Rosteritem;
use Bishopm\Churchnet\Models\Rostergroup;
use Bishopm\Churchnet\Models\Service;
use Bishopm\Churchnet\Models\Society;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Services\BulkSMSService;
use Bishopm\Churchnet\Services\SMSPortalService;
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
                    $indivs = explode(',', $ri->individuals);
                    $people=array();
                    foreach ($indivs as $indiv) {
                        if ($indiv >= 1) {
                            $person = Individual::find($indiv);
                            if ($person) {
                                $dum['label']=substr($person->firstname, 0, 1) . " " . $person->surname;
                                $dum['id']=$person->id;
                            } else {
                                $dum['label']=$indiv;
                                $dum['id']=$indiv;
                            }
                            $people[]=$dum;
                        }
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

    public function messages($id)
    {
        $this->roster=Roster::find($id);
        if (date('l')==$this->roster->dayofweek) {
            $nextday = date('Y-m-d');
        } else {
            $nextday = date('Y-m-d', strtotime('next ' . $this->roster->dayofweek));
        }
        $messages = array();
        $items = Rosteritem::where('rosterdate', $nextday)->with('rostergroup')->whereHas('rostergroup', function ($query) {
            $query->where('roster_id', '=', $this->roster->id);
        })->get();
        $extras = array();
        $dum = array();
        foreach ($items as $item) {
            $individs = explode(',', $item->individuals);
            foreach ($individs as $individ) {
                $message = new \stdClass;
                $indiv = Individual::find($individ);
                $message->firstname = $indiv->firstname;
                $message->surname = $indiv->surname;
                $message->cellphone = $indiv->cellphone;
                $messages[$individ]['person']=$message;
                $messages[$individ]['groups'][$item->rostergroup->group->id]=$item->rostergroup->group->groupname;
                if ($item->rostergroup->extrainfo == 'yes') {
                    $extras[$item->rostergroup->group->id]=$item->rostergroup->group->groupname;
                }
            }
        }
        $msgs=array();
        foreach ($messages as $message) {
            $message['text'] = $message['person']->firstname . ", " . $this->roster->message . " (" . implode(', ', $message['groups']) . ")";
            $msgs['texts'][]= $message;
        }
        $msgs['roster']['name'] = $this->roster->name;
        $msgs['roster']['date'] = $nextday;
        $msgs['roster']['extras'] = array_unique($extras);
        return $msgs;
    }

    public function sendmessages(Request $request)
    {
        $society = Society::find($request->society);
        if ($society['sms_service']=='bulksms') {
            $smss = new BulkSMSService($society['sms_user'], $society['sms_pw']);
        } elseif ($society['sms_service']=='smsportal') {
            $smss = new SMSPortalService($society['sms_user'], $society['sms_pw']);
        }
        $credits=$smss->get_credits($society['sms_user'], $society['sms_pw']);
        if (count($request->messages)>$credits) {
            return "Insufficient Bulk SMS credits to send SMS";
        }
        $results = array();
        foreach ($request->messages as $message) {
            $msgtxt=$message['text'];
            if (array_key_exists('extras', $message)) {
                $msgtxt = $msgtxt . ' (' . $message['extras'] . ')';
            }
            $msisdn = "+27" . substr($message['person']['cellphone'], 1);
            if ((preg_match("/^[0-9]+$/", $message['person']['cellphone'])) and (strlen($message['person']['cellphone'])==10)) {
                if ($society['sms_service']=='bulksms') {
                    $msg=array('to'=>$msisdn, 'body'=>$msgtxt);
                } elseif ($society['sms_service']=='smsportal') {
                    $msg=array('Destination'=>$msisdn, 'Content'=>$msgtxt);
                }
                $res=$smss->send_message($msg);
                $results[]=array('to'=>$msisdn, 'body'=>$msgtxt, 'result'=>$res);
            }
        }
        return $results;
    }

    public function edit($id)
    {
        return Roster::with('rostergroups.group', 'society')->where('id', $id)->first();
    }

    public function store(Request $request)
    {
        $roster = Roster::create(['name' => $request->name, 'dayofweek' => $request->dayofweek, 'society_id' => $request->society_id, 'message' => $request->message]);
        return $roster;
    }

    public function update($id, Request $request)
    {
        $roster = Roster::find($id);
        $roster->name = $request->name;
        $roster->dayofweek=$request->dayofweek;
        $roster->message=$request->message;
        $roster->save();
        return $roster;
    }

    public function storeitem(Request $request)
    {
        $delete = Rosteritem::where('rostergroup_id', $request->rostergroup_id)->where('rosterdate', $request->rosterdate)->first();
        if ($delete) {
            $delete->delete();
        }
        $rosteritem = Rosteritem::create(['rostergroup_id' => $request->rostergroup_id, 'rosterdate' => $request->rosterdate, 'individuals' => implode(",", $request->individuals)]);
        return $rosteritem;
    }

    public function storerostergroup(Request $request)
    {
        $rostergroup = Rostergroup::create(['group_id' => $request->group_id, 'roster_id' => $request->roster_id, 'maxpeople' => $request->maxpeople, 'extrainfo' => $request->extrainfo]);
        return Rostergroup::with('group')->where('id', $rostergroup->id)->first();
    }

    public function deleterostergroup($id)
    {
        $rostergroup = Rostergroup::find($id);
        $rostergroup->delete();
        return "Roster group deleted";
    }
}
