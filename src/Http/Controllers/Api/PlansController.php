<?php
namespace Bishopm\Churchnet\Http\Controllers\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Models\Plan;
use Auth;
use Bishopm\Churchnet\Http\Requests\PlansRequest;
use Redirect;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\Person;
use Bishopm\Churchnet\Repositories\WeekdaysRepository;
use Bishopm\Churchnet\Repositories\MeetingsRepository;
use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Repositories\PreachersRepository;
use Bishopm\Churchnet\Repositories\ServicesRepository;
use Bishopm\Churchnet\Repositories\CircuitsRepository;
use Bishopm\Churchnet\Repositories\PlansRepository;
use Bishopm\Churchnet\Repositories\LabelsRepository;

class PlansController extends Controller
{
    private $weekdays;
    private $meetings;
    private $societies;
    private $preachers;
    private $services;
    private $circuit;
    private $plans;
    private $labels;
  
    public function __construct(
  
        WeekdaysRepository $weekdays,
        MeetingsRepository $meetings,
        SocietiesRepository $societies,
        PreachersRepository $preachers,
        ServicesRepository $services,
        CircuitsRepository $circuit,
        PlansRepository $plans,
        LabelsRepository $labels
    ) {
        $this->weekdays=$weekdays;
        $this->meetings=$meetings;
        $this->societies=$societies;
        $this->preachers=$preachers;
        $this->services=$services;
        $this->circuit=$circuit;
        $this->plans=$plans;
        $this->labels=$labels;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('connexion::plans.edit');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function currentplan($status="view")
    {
        $one=range(2, 4);
        $two=range(5, 7);
        $three=range(8, 10);
        $four=range(11, 12);
        $m=intval(date('n'));
        $y=intval(date('Y'));
        if (in_array($m, $one)) {
            $this->show($y, 1, $status);
        } elseif (in_array($m, $two)) {
            $this->show($y, 2, $status);
        } elseif (in_array($m, $three)) {
            $this->show($y, 3, $status);
        } elseif (in_array($m, $four)) {
            $this->show($y, 4, $status);
        } elseif ($m==1) {
            $this->show($y-1, 4, $status);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($circuit, $yy, $qq)
    {
        $data=array();
        if ($qq=="current") {
            $one=range(2, 4);
            $two=range(5, 7);
            $three=range(8, 10);
            $four=range(11, 12);
            $m=intval(date('n'));
            $y=intval(date('Y'));
            if (in_array($m, $one)) {
                $qq=1;
            } elseif (in_array($m, $two)) {
                $qq=2;
            } elseif (in_array($m, $three)) {
                $qq=3;
            } elseif (in_array($m, $four)) {
                $qq=4;
            } elseif ($m==1) {
                $yy=$yy-1;
                $qq=4;
            }
        }
        $fin=array();
        $fm=2;
        $m1=$qq*3-3+$fm;
        $y1=$yy;
        $m2=$qq*3-2+$fm;
        $y2=$yy;
        $m3=$qq*3-1+$fm;
        $y3=$yy;
        if ($m2>12) {
            $m2=$m2-12;
            $y2=$y2+1;
        }
        if ($m3>12) {
            $m3=$m3-12;
            $y3=$y3+1;
        }
        $firstDateTime=mktime(0, 0, 0, $m1, 1, $y1);
        $firstDay=date("N", $firstDateTime);
        $firstSunday=date("d M Y", mktime(0, 0, 0, $m1, 8-$firstDay, $y1));
        $lastSunday=strtotime($firstSunday);
        $lastDay=mktime(23, 59, 59, $m3, cal_days_in_month(CAL_GREGORIAN, $m3, $y3), $y3);
        $extras=$this->weekdays->valueBetween('servicedate', $firstDateTime, $lastDay);
        $data['meetings']=$this->meetings->valueBetween('meetingdatetime', $firstDateTime, $lastDay);
        $dum['dt']=$lastSunday;
        $dum['yy']=intval(date("Y", $lastSunday));
        $dum['mm']=intval(date("n", $lastSunday));
        $dum['dd']=intval(date("j", $lastSunday));
        $sundays[]=$dum;
        $data['societies']=$this->societies->allforcircuit($circuit);
        $data['circuit']=$this->circuit->find($circuit);
        $data['preachers']=Person::where('circuit_id', $data['circuit']->id)->with('tags')->where('status', 'preacher')->orderBy('surname')->orderBy('firstname')->get();
        $data['ministers']=Person::withAnyTags(['Circuit minister', 'Superintendent'], 'minister')->with('tags')->where('circuit_id', $data['circuit']->id)->where('status', 'minister')->orderBy('surname')->orderBy('firstname')->get();
        $data['supernumeraries']=Person::withAnyTags(['Supernumerary minister'], 'minister')->with('tags')->where('circuit_id', $data['circuit']->id)->where('status', 'minister')->orderBy('surname')->orderBy('firstname')->get();
        $data['guests']=array();
        while (date($lastSunday+604800<=$lastDay)) {
            $lastSunday=$lastSunday+604800;
            $dum['dt']=$lastSunday;
            $dum['yy']=intval(date("Y", $lastSunday));
            $dum['mm']=intval(date("n", $lastSunday));
            $dum['dd']=intval(date("j", $lastSunday));
            $sundays[]=$dum;
        }
        if (count($extras)) {
            $xco=0;
            for ($q = 0; $q < count($sundays); $q++) {
                if (($xco<count($extras)) and ($extras[$xco]->servicedate < $sundays[$q]['dt'])) {
                    $dum['dt']=$extras[$xco]->servicedate;
                    $dum['yy']=intval(date("Y", $extras[$xco]->servicedate));
                    $dum['mm']=intval(date("n", $extras[$xco]->servicedate));
                    $dum['dd']=intval(date("j", $extras[$xco]->servicedate));
                    $data['sundays'][]=$dum;
                    $xco++;
                    $q=$q-1;
                } else {
                    $data['sundays'][]=$sundays[$q];
                }
            }
        } else {
            $data['sundays']=$sundays;
        }
        $pm1 = $this->plans->preachingmonth($data['circuit']->id, $y1, $m1);
        foreach ($pm1 as $p1) {
            $soc=$this->societies->find($p1->society_id)->society;
            $ser=$this->services->find($p1->service_id)->servicetime;
            if ($p1->person_id) {
                $data['fin'][$soc][$p1->planyear][$p1->planmonth][$p1->planday][$ser]['preacher']=$p1->person_id;
                $data['fin'][$soc][$p1->planyear][$p1->planmonth][$p1->planday][$ser]['pname']=substr($p1->person->firstname, 0, 1) . " " . $p1->person->surname;
            } else {
                $data['fin'][$soc][$p1->planyear][$p1->planmonth][$p1->planday][$ser]['preacher']="";
            }
            if ($p1->servicetype) {
                $data['fin'][$soc][$p1->planyear][$p1->planmonth][$p1->planday][$ser]['tname']=$p1->servicetype;
            } else {
                $data['fin'][$soc][$p1->planyear][$p1->planmonth][$p1->planday][$ser]['label']="";
            }
            if ($p1->trialservice) {
                $data['fin'][$soc][$p1->planyear][$p1->planmonth][$p1->planday][$ser]['trial']=$p1->trialservice;
            }
        }
        $pm2 = $this->plans->preachingmonth($data['circuit']->id, $y2, $m2);
        foreach ($pm2 as $p2) {
            $soc=$this->societies->find($p2->society_id)->society;
            $ser=$this->services->find($p2->service_id)->servicetime;
            if ($p2->person_id) {
                $data['fin'][$soc][$p2->planyear][$p2->planmonth][$p2->planday][$ser]['preacher']=$p2->person_id;
                $data['fin'][$soc][$p2->planyear][$p2->planmonth][$p2->planday][$ser]['pname']=substr($p2->person->firstname, 0, 1) . " " . $p2->person->surname;
            } else {
                $data['fin'][$soc][$p2->planyear][$p2->planmonth][$p2->planday][$ser]['preacher']="";
            }
            if ($p2->servicetype) {
                $data['fin'][$soc][$p2->planyear][$p2->planmonth][$p2->planday][$ser]['tname']=$p2->servicetype;
            } else {
                $data['fin'][$soc][$p2->planyear][$p2->planmonth][$p2->planday][$ser]['label']="";
            }
            if ($p2->trialservice) {
                $data['fin'][$soc][$p2->planyear][$p2->planmonth][$p2->planday][$ser]['trial']=$p2->trialservice;
            }
        }
        $pm3 = $this->plans->preachingmonth($data['circuit']->id, $y3, $m3);
        foreach ($pm3 as $p3) {
            $soc=$this->societies->find($p3->society_id)->society;
            $ser=$this->services->find($p3->service_id)->servicetime;
            if ($p3->person_id) {
                $data['fin'][$soc][$p3->planyear][$p3->planmonth][$p3->planday][$ser]['preacher']=$p3->person_id;
                $data['fin'][$soc][$p3->planyear][$p3->planmonth][$p3->planday][$ser]['pname']=substr($p3->person->firstname, 0, 1) . " " . $p3->person->surname;
            } else {
                $data['fin'][$soc][$p3->planyear][$p3->planmonth][$p3->planday][$ser]['preacher']="";
            }
            if ($p3->servicetype) {
                $data['fin'][$soc][$p3->planyear][$p3->planmonth][$p3->planday][$ser]['tname']=$p3->servicetype;
            } else {
                $data['fin'][$soc][$p3->planyear][$p3->planmonth][$p3->planday][$ser]['label']="";
            }
            if ($p3->trialservice) {
                $data['fin'][$soc][$p3->planyear][$p3->planmonth][$p3->planday][$ser]['trial']=$p3->trialservice;
            }
        }
        foreach ($this->labels->allforcircuitonly($circuit) as $label) {
            $data['labels'][]=$label->label;
        }
        if ($qq==1) {
            $data['prev']="plan/" . strval($yy-1) . "/4";
        } else {
            $data['prev']="plan/$yy/" . strval($qq-1);
        }
        if ($qq==4) {
            $data['next']="plan/" . strval($yy+1) . "/1";
        } else {
            $data['next']="plan/$yy/" . strval($qq+1);
        }
        return $data;
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($circuit, $box, $val)
    {
        $fields=explode('_', $box);
        $pa=array('circuit_id'=>$circuit,'society_id'=>$fields[1],'service_id'=>$fields[2],'planyear'=>$fields[3],'planmonth'=>$fields[4],'planday'=>$fields[5]);
        $plan=Plan::firstOrCreate($pa);
        if ($fields[0]=="t") {
            if (is_numeric($val)) {
                $plan->trialservice=$val;
                $plan->servicetype=null;
            } elseif ($val<>"blank") {
                $plan->trialservice=null;
                $plan->servicetype=$val;
            } elseif ($val=="blank") {
                $plan->trialservice=null;
                $plan->servicetype=null;
            }
        } elseif ($fields[0]=="p") {
            if ($val<>"blank") {
                $plan->person_id=explode('_', $val)[1];
            } else {
                $plan->person_id=null;
            }
        }
        $plan->save();
    }
}
