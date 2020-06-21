<?php
namespace Bishopm\Churchnet\Http\Controllers\Web;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Auth;
use Bishopm\Churchnet\Libraries\Fpdf\Fpdf;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\Person;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Household;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Circuit;
use Bishopm\Churchnet\Models\District;
use Bishopm\Churchnet\Models\Denomination;
use Bishopm\Churchnet\Models\Meeting;
use Bishopm\Churchnet\Repositories\WeekdaysRepository;
use Bishopm\Churchnet\Repositories\MeetingsRepository;
use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Repositories\PeopleRepository;
use Bishopm\Churchnet\Repositories\PlansRepository;
use Bishopm\Churchnet\Repositories\ServicesRepository;
use Bishopm\Churchnet\Repositories\CircuitsRepository;
use Bishopm\Churchnet\Repositories\LabelsRepository;
use Bishopm\Churchnet\Repositories\TagsRepository;

class PlansController extends Controller
{
    private $weekdays;
    private $societies;
    private $people;
    private $plans;
    private $services;
    private $circuit;
    private $labels;
    private $tag;

    public function __construct(
        WeekdaysRepository $weekdays,
        SocietiesRepository $societies,
        PeopleRepository $people,
        PlansRepository $plans,
        ServicesRepository $services,
        CircuitsRepository $circuit,
        LabelsRepository $labels,
        TagsRepository $tag
    ) {
        $this->weekdays=$weekdays;
        $this->societies=$societies;
        $this->people=$people;
        $this->plans=$plans;
        $this->services=$services;
        $this->circuit=$circuit;
        $this->labels=$labels;
        $this->tag=$tag;
    }

    public function plan($slug, $y='', $m='')
    {
        if (is_numeric($slug)) {
            $this->circuit=$this->circuit->find($slug);
        } else {
            $this->circuit=$this->circuit->findBySlug($slug);
        }
        $planmonth = $this->circuit->plan_month;
        if (($y=='') or ($m=='')) {
            $m=intval(date('n'));
            $y=intval(date('Y'));
        }
        if ($planmonth==1) {
            $one=array(1,2,3);
            $two=array(4,5,6);
            $three=array(7,8,9);
            $four=array(10,11,12);
        } elseif ($planmonth==2) {
            $one=array(2,3,4);
            $two=array(5,6,7);
            $three=array(8,9,10);
            $four=array(11,12,1);
        } elseif ($planmonth==3) {
            $one=array(3,4,5);
            $two=array(6,7,8);
            $three=array(9,10,11);
            $four=array(12,1,2);
        }
        if ((($m==1) and ($planmonth > 1)) or (($m==2) and ($planmonth == 3))) {
            $y=$y-1;
        }
        if (in_array($m, $one)) {
            $this->show($y, 1);
        } elseif (in_array($m, $two)) {
            $this->show($y, 2);
        } elseif (in_array($m, $three)) {
            $this->show($y, 3);
        } elseif (in_array($m, $four)) {
            $this->show($y, 4);
        }
    }

    public function show($yy, $qq)
    {
        $fm = $this->circuit->plan_month;
        $data=array();
        $fin=array();
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
        $previousDateTime = strtotime("-3 months", $firstDateTime);
        $nextDateTime = strtotime("+3 months", $firstDateTime);
        $firstDay=date("N", $firstDateTime);
        $firstSunday=date("d M Y", mktime(0, 0, 0, $m1, 8-$firstDay, $y1));
        $lastSunday=strtotime($firstSunday);
        $lastDay=mktime(23, 59, 59, $m3, cal_days_in_month(CAL_GREGORIAN, $m3, $y3), $y3);
        $previouslastDay=strtotime("+3 months", $previousDateTime) - 60*60*24;
        $nextlastDay=strtotime("+3 months", $nextDateTime) - 60*60*24;
        $extras=$this->weekdays->valueBetween('servicedate', $firstDateTime, $lastDay);
        $data['meetings']=Meeting::where('meetingdatetime', '>=', $firstDateTime)->where('meetingdatetime', '<=', $lastDay)->where('preachingplan', 'yes')->where('meetable_type','Bishopm\\Churchnet\\Models\\Circuit')->where('meetable_id',$this->circuit->id)
            ->orWhere('meetingdatetime', '>=', $previousDateTime)->where('meetingdatetime', '<=', $previouslastDay)->where('preachingplan', 'next')->where('meetable_type','Bishopm\\Churchnet\\Models\\Circuit')->where('meetable_id',$this->circuit->id)
            ->orWhere('meetingdatetime', '>=', $nextDateTime)->where('meetingdatetime', '<=', $nextlastDay)->where('preachingplan', 'previous')->where('meetable_type','Bishopm\\Churchnet\\Models\\Circuit')->where('meetable_id',$this->circuit->id)
            ->orderBy('meetingdatetime', 'ASC')->get();
        $dum['dt']=$lastSunday;
        $dum['yy']=intval(date("Y", $lastSunday));
        $dum['mm']=intval(date("n", $lastSunday));
        $dum['dd']=intval(date("j", $lastSunday));
        $sundays[]=$dum;
        $data['societies']=$this->societies->allforcircuit($this->circuit->id);
        $data['circuit']=$this->circuit;
        $district=District::with('individuals', 'denomination.individuals')->find($data['circuit']->district_id);
        $data['preachers']=$this->circuit->tagged('Local preacher, Local preacher on trial')->get();
        $ministers=$this->circuit->tagged('Circuit minister')->get();
        $deacons=$this->circuit->tagged('Deacon')->get();
        $data['ministers'] = $ministers->merge($deacons);
        $data['supernumeraries']=$this->circuit->tagged('Supernumerary minister')->get();
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
        $pm1 = $this->plans->preachingmonth($this->circuit->id, $y1, $m1);
        foreach ($pm1 as $p1) {
            $soc=$this->societies->find($p1->society_id)->society;
            $ser=$this->services->find($p1->service_id)->servicetime;
            if ($p1->person_id) {
                $data['fin'][$soc][$p1->planyear][$p1->planmonth][$p1->planday][$ser]['preacher']=$p1->person_id;
                $data['fin'][$soc][$p1->planyear][$p1->planmonth][$p1->planday][$ser]['pname']=substr($p1->person->individual->firstname, 0, 1) . " " . $p1->person->individual->surname;
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
        $pm2 = $this->plans->preachingmonth($this->circuit->id, $y2, $m2);
        foreach ($pm2 as $p2) {
            $soc=$this->societies->find($p2->society_id)->society;
            $ser=$this->services->find($p2->service_id)->servicetime;
            if ($p2->person) {
                $data['fin'][$soc][$p2->planyear][$p2->planmonth][$p2->planday][$ser]['preacher']=$p2->person_id;
                $data['fin'][$soc][$p2->planyear][$p2->planmonth][$p2->planday][$ser]['pname']=substr($p2->person->individual->firstname, 0, 1) . " " . $p2->person->individual->surname;
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
        $pm3 = $this->plans->preachingmonth($this->circuit->id, $y3, $m3);
        foreach ($pm3 as $p3) {
            $soc=$this->societies->find($p3->society_id)->society;
            $ser=$this->services->find($p3->service_id)->servicetime;
            if ($p3->person) {
                $data['fin'][$soc][$p3->planyear][$p3->planmonth][$p3->planday][$ser]['preacher']=$p3->person_id;
                $data['fin'][$soc][$p3->planyear][$p3->planmonth][$p3->planday][$ser]['pname']=substr($p3->person->individual->firstname, 0, 1) . " " . $p3->person->individual->surname;
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
        foreach ($this->labels->allforcircuitonly($this->circuit->id) as $label) {
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
        $dat=$data;
        $pdf = new Fpdf();
        $pdf->AddPage('L');
        $logopath=base_path() . '/public/vendor/bishopm/images/mcsa.jpg';
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->SetFont('Arial', '', 9);
        $num_ser=0;
        foreach ($dat['societies'] as $s1) {
            foreach ($s1['services'] as $se1) {
                $num_ser++;
            }
        }
        $header=20;
        $left_side=5;
        $left_edge=40;
        $num_soc=count($dat['societies']);
        $num_sun=count($dat['sundays']);
        $soc_width=$left_edge-17;
        $pg_height=210;
        $pg_width=297;
        $y=$header;
        $x=$left_edge;
        if ($num_ser) {
            $y_add=($pg_height-$header-3*($num_ser-$num_soc))/$num_ser;
        } else {
            $y_add=16;
        }
        if ($y_add>16) {
            $y_add=16;
        }
        $x_add=($pg_width-5-$left_edge)/$num_sun;
        $toprow=true;
        $pdf->Image($logopath, 5, 5, 0, 21);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->text($left_side+$soc_width, 10, "THE METHODIST CHURCH OF SOUTHERN AFRICA: " . strtoupper($dat['circuit']['circuit']) . " " . $dat['circuit']['circuitnumber']);
        $pdf->text($left_side+$soc_width, 17, "PREACHING PLAN: " . strtoupper(date("F Y", $dat['sundays'][0]['dt'])) . " - " . strtoupper(date("F Y", $dat['sundays'][count($dat['sundays'])-1]['dt'])));
        $socids=array();
        $allsocieties="";
        $someservices = false;
        foreach ($dat['societies'] as $soc) {
            $socids[]=$soc->id;
            $allsocieties = $allsocieties . $soc['society'] . ", ";
            $firstserv=true;
            foreach ($soc['services'] as $ser) {
                $someservices = true;
                if ($firstserv) {
                    $y=$y+$y_add;
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->rect($left_side, $y-2, ($pg_width-2*$left_side), $y_add+($y_add)*(count($soc['services'])-1)-(3*(count($soc['services'])-1)), 'D');
                    $pdf->setxy($left_side, $y);
                    if (count($soc['services'])==1) {
                        $pdf->setxy($left_side, $y);
                    } else {
                        $pdf->setxy($left_side, $y+(($y_add-3)*(count($soc['services'])-1)/2));
                    }
                    $font_size = 8;
                    $decrement_step = 0.1;
                    $pdf->SetFont('Arial', 'B', $font_size);
                    while ($pdf->GetStringWidth($soc['society']) > $soc_width-2) {
                        $pdf->SetFontSize($font_size -= $decrement_step);
                    }
                    $pdf->cell($soc_width, $y_add-3, $soc['society'], 0, 0, 'R');
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->setxy($left_side+$soc_width, $y);
                    $pdf->cell(12, $y_add-3, $ser['servicetime'], 0, 0, 'C');
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetDrawColor(0, 0, 0);
                } else {
                    $y=$y+$y_add-3;
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->setxy($left_side+$soc_width, $y);
                    $pdf->cell(12, $y_add-3, $ser['servicetime'], 0, 0, 'C');
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->SetTextColor(0, 0, 0);
                }
                $firstserv=false;
                foreach ($dat['sundays'] as $sun) {
                    if ($toprow) {
                        // Weekly dates
                        $pdf->SetFont('Arial', 'B', 8);
                        if (date("D", $sun['dt'])=="Sun") {
                            $pdf->setxy($x, $header+2);
                            $pdf->cell($x_add, $y_add-6, date("j M", $sun['dt']), 0, 0, 'C');
                        } else {
                            $wd=$this->weekdays->findfordate($dat['circuit']['id'], $sun['dt']);
                            $pdf->setxy($x, $header+4);
                            $pdf->SetFont('Arial', '', 7);
                            if (isset($wd->description)) {
                                $pdf->cell($x_add, $y_add-6, $wd->description, 0, 0, 'C');
                            }
                            $pdf->SetFont('Arial', 'B', 8);
                            $pdf->setxy($x, $header);
                            $pdf->cell($x_add, $y_add-6, date("j M", $sun['dt']), 0, 0, 'C');
                        }
                    }
                    if (isset($dat['fin'][$soc['society']][$sun['yy']][$sun['mm']][$sun['dd']][$ser['servicetime']]['tname'])) {
                        $tagadd=1;
                        $pdf->setxy($x, $y-2);
                        $pdf->SetFont('Arial', 'B', 7.5);
                        $pdf->cell($x_add, $y_add-2, $dat['fin'][$soc['society']][$sun['yy']][$sun['mm']][$sun['dd']][$ser['servicetime']]['tname'], 0, 0, 'C');
                    } else {
                        $tagadd=0;
                    }
                    if (isset($dat['fin'][$soc['society']][$sun['yy']][$sun['mm']][$sun['dd']][$ser['servicetime']]['pname'])) {
                        $pdf->setxy($x, $y+$tagadd);
                        $pname=utf8_decode($dat['fin'][$soc['society']][$sun['yy']][$sun['mm']][$sun['dd']][$ser['servicetime']]['pname']);
                        $font_size = 8;
                        $decrement_step = 0.1;
                        $pdf->SetFont('Arial', '', $font_size);
                        while ($pdf->GetStringWidth($pname) > $x_add-1) {
                            $pdf->SetFontSize($font_size -= $decrement_step);
                        }
                        $pdf->cell($x_add, $y_add-3, $pname, 0, 0, 'C');
                    }
                    if (isset($dat['fin'][$soc['society']][$sun['yy']][$sun['mm']][$sun['dd']][$ser['servicetime']]['trial'])) {
                        $pdf->setxy($x, $y+$tagadd+2.5);
                        $trial=$this->people->find($dat['fin'][$soc['society']][$sun['yy']][$sun['mm']][$sun['dd']][$ser['servicetime']]['trial']);
                        $tname="[" . utf8_decode(substr($trial->individual->firstname, 0, 1) . " " . $trial->individual->surname) . "]";
                        $pdf->SetFont('Arial', '', 6.5);
                        $pdf->cell($x_add, $y_add-3, $tname, 0, 0, 'C');
                    }
                    $x=$x+$x_add;
                }
                $toprow=false;
                $x=$left_edge;
            }
        }
        $x2=$x;
        foreach ($dat['sundays'] as $sun2) {
            if ($someservices) {
                $pdf->line($x2, $header+$y_add-2, $x2, $y+$y_add-2);
                $x2=$x2+$x_add;
            } else {
                $pdf->setxy(27, 25);
                $pdf->SetFont('Arial', '', 10);
                if (strlen($allsocieties)) {
                    $pdf->multicell(180, 5, 'The following societies are listed in this circuit: ' . substr($allsocieties,0,-2) . '. To set up the preaching plan, service times still need to be added for each service at each society.');
                } else {
                    $pdf->multicell(180, 5, 'No societies are listed in this circuit. To set up the preaching plan, societies and service times still need to be added to the system.');
                }
            }
        }
        $pdf->AddPage('L');
        $pdf->Image($logopath, 10, 5, 0, 21);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->text($left_side+$soc_width+8, 10, "THE METHODIST CHURCH OF SOUTHERN AFRICA: " . strtoupper($dat['circuit']['circuit']) . " " . $dat['circuit']['circuitnumber']);
        $pdf->text($left_side+$soc_width+8, 17, "PREACHING PLAN: " . strtoupper(date("F Y", $dat['sundays'][0]['dt'])) . " - " . strtoupper(date("F Y", $dat['sundays'][count($dat['sundays'])-1]['dt'])));
        $pfin=array();
        foreach ($dat['preachers'] as $preacher1) {
            $dum=array();
            $thissoc=$this->societies->find($preacher1->individual->household->society_id)->society;
            $dum['name']=$preacher1->individual->title . " " . $preacher1->individual->firstname . " " . $preacher1->individual->surname;
            if ($this->tag->checktag($preacher1, 'Emeritus preacher')) {
                $dum['name'] = $dum['name'] . "*";
            }
            $dum['soc']=$preacher1->individual->household->society_id;
            $dum['cellphone']=$preacher1->individual->cellphone;
            $dum['inducted']=$preacher1->inducted;
            if ($this->tag->checktag($preacher1, 'Local preacher on trial')) {
                $dum['inducted']="Trial";
            }
            if (!$dum['inducted']) {
                $vdum['9999' . $preacher1->individual->surname . $preacher1->individual->firstname]=$dum;
            } else {
                $vdum[$preacher1->inducted . $preacher1->individual->surname . $preacher1->individual->firstname]=$dum;
            }
        }
        if (isset($vdum)){
            ksort($vdum);
            foreach ($vdum as $vd) {
                $thissoc=$this->societies->find($vd['soc'])->society;
                $pfin[$thissoc][]=$vd;
            }
        }
        $cols=4;
        $spacer=5;
        $col_width=($pg_width-(2*$left_side))/$cols;
        $y=30;
        $col=1;
        $pdf->SetFont('Arial', '', 8);
        foreach ($district->denomination->individuals as $denoms) {
            $pdf->text($left_side+$spacer, $y, $denoms->pivot->description . ": " . $denoms->title . " " . substr($denoms->firstname, 0, 1) . " " . $denoms->surname);
            $y=$y+4;
        }
        foreach ($district->individuals as $dist) {
            $pdf->text($left_side+$spacer, $y, $dist->pivot->description . ": " . $dist->title . " " . substr($dist->firstname, 0, 1) . " " . $dist->surname);
            $y=$y+4;
        }
        $y=$y+2;
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->text($left_side+$spacer, $y, "Circuit Staff");
        $y=$y+4;
        $pdf->SetFont('Arial', '', 8);
        $sortedministers=array();
        foreach ($dat['ministers'] as $dm) {
            $ndx = $dm->individual->surname . $dm->individual->firstname;
            $sortedministers[$ndx]=$dm;
        }
        ksort($sortedministers);
        foreach ($sortedministers as $min) {
            if ($this->tag->checktag($min, 'Superintendent')) {
                $super = " [Supt]";
            } else {
                $super="";
            }
            $pdf->text($left_side+$spacer, $y, $min->individual->title . " " . substr($min->individual->firstname, 0, 1) . " " . $min->individual->surname . " (" . $min->individual->cellphone . ")" . $super);
            $y=$y+4;
        }
        if (!count($dat['ministers'])) {
            $pdf->text($left_side+$spacer, $y, "No ministers have been added");
            $y=$y+4;
        }
        if (isset($dat['supernumeraries'])) {
            if (count($dat['supernumeraries'])){
                $y=$y+2;
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->text($left_side+$spacer, $y, "Supernumerary Ministers");
                $y=$y+4;
            }
            $pdf->SetFont('Arial', '', 8);
            foreach ($dat['supernumeraries'] as $supm) {
                $pdf->text($left_side+$spacer, $y, $supm->individual->title . " " . substr($supm->individual->firstname, 0, 1) . " " . $supm->individual->surname . " (" . $supm->individual->cellphone . ")");
                $y=$y+4;
            }
        }
        $y=$y+2;
        $pdf->SetFont('Arial', '', 8);
        $officers = Individual::societymember($socids)->withAnyTags('circuit steward')->get();
        $subhead="";
        if (count($officers)) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->text($left_side+$spacer, $y, "Circuit Stewards");
            $pdf->SetFont('Arial', '', 8);
            foreach ($officers as $officer) {
                $y=$y+4;
                $pdf->text($left_side+$spacer, $y, $officer->title . " " . substr($officer->firstname, 0, 1) . " " . $officer->surname . " (" . $officer->cellphone . ")");
            }
        }
        $pdf->SetFont('Arial', 'B', 11);
        $y=$y+6;
        $treasurer = Individual::societymember($socids)->withAnyTags('Circuit treasurer')->first();
        if ($treasurer) {
            $pdf->text($left_side+$spacer, $y, "Circuit Treasurer");
            $pdf->SetFont('Arial', '', 8);
            $y=$y+4;
            $pdf->text($left_side+$spacer, $y, $treasurer->title . " " . substr($treasurer->firstname, 0, 1) . " " . $treasurer->surname . " (" . $treasurer->cellphone . ")");
            $pdf->SetFont('Arial', 'B', 11);
            $y=$y+6;
        }
        $csecretary = Individual::societymember($socids)->withAnyTags('Circuit secretary')->first();
        if ($csecretary) {
            $pdf->text($left_side+$spacer, $y, "Circuit Secretary");
            $pdf->SetFont('Arial', '', 8);
            $y=$y+4;
            $pdf->text($left_side+$spacer, $y, $csecretary->title . " " . substr($csecretary->firstname, 0, 1) . " " . $csecretary->surname  . " (" . $csecretary->cellphone . ")");
            $pdf->SetFont('Arial', 'B', 11);
            $y=$y+6;
        }
        $y=$y+2;
        if (count($dat['meetings'])) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->text($left_side+$spacer, $y, "Circuit Meetings");
            $y=$y+4;
            foreach ($dat['meetings'] as $meeting) {
                $x=$left_side+$spacer+($col-1)*$col_width;
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->text($x, $y, $meeting['description']);
                $pdf->SetFont('Arial', '', 8);
                $y=$y+4;
                $msoc=$this->societies->find($meeting['society_id'])->society;
                $pdf->text($x, $y, date("d M Y H:i", $meeting['meetingdatetime']) . " (" . $msoc . ")");
                $y=$y+4;
            }
        }
        $y=$y+2;
        $col++;
        $x=$left_side+$spacer+($col-1)*$col_width;
        $y=30;
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->text($x, $y, "Local Preachers");
        $supervisor=$this->circuit->tagged('Circuit supervisor of studies')->first();
        if ($supervisor) {
            $y=$y+4;
            $pdf->SetFont('Arial', '', 8);
            $pdf->text($x, $y, "Supervisor of studies: " . $supervisor->individual->title . ' ' . substr($supervisor->individual->firstname, 0, 1) . ' ' . $supervisor->individual->surname);
        }
        $lpsec=$this->circuit->tagged('Local preachers secretary')->first();
        if ($lpsec) {
            $y=$y+4;
            $pdf->SetFont('Arial', '', 8);
            $pdf->text($x, $y, "Local Preachers Secretary: " . $lpsec->individual->title . ' ' . substr($lpsec->individual->firstname, 0, 1) . ' ' . $lpsec->individual->surname);
        }
        $y=$y+4;
        $ythresh=200;
        ksort($pfin);
        foreach ($pfin as $key=>$soc) {
            if ($y>$ythresh-6) {
                $col++;
                $y=30;
            }
            $x=$left_side+$spacer+($col-1)*$col_width;
            $pdf->SetFont('Arial', 'B', 9);
            $y=$y+2;
            $pdf->text($x, $y, $key);
            $y=$y+4;
            $pdf->SetFont('Arial', '', 8);
            foreach ($soc as $pre) {
                if ($y>$ythresh) {
                    $col++;
                    $x=$left_side+$spacer+($col-1)*$col_width;
                    $y=30;
                }
                $pre['name']=utf8_decode($pre['name']);
                //if (($pre['position']=="Local preacher") or ($pre['position']=="On trial preacher") or ($pre['position']=="Emeritus preacher")) {
                $pdf->text($x+2, $y, $pre['inducted']);
                $pdf->text($x+10, $y, $pre['name'] . " (" . $pre['cellphone'] . ")");
                $y=$y+4;
                //}
            }
        }
        $pdf->SetFont('Arial', '', 8);
        if (count($pfin)){
            $y=$y+4;
            $pdf->text($x+2, $y, "* Emeritus");
        } else {
            $pdf->text($x, $y, "No preachers have been added yet");
        }
        $pdf->Output();
        exit;
    }

    public function groupreport(Request $request)
    {
        $group = json_decode($request->group);
        $society = Society::find($group->society_id);
        $indivs = json_decode($request->members);
        $logopath=base_path() . '/public/vendor/bishopm/images/mcsa.jpg';
        $pg=1;
        $yy=40;
        $pdf = new Fpdf();
        if ($group->memberunit == "household"){
            $iids = array();
            foreach ($indivs as $indiv) {
                $iids[]=$indiv->id;
            }
            $allindivs = Individual::whereIn('id',$iids)->select('household_id')->get()->toArray();
            $hids = array();
            foreach ($allindivs as $ai) {
                if (!in_array($ai['household_id'],$hids)){
                    $hids[] = $ai['household_id'];
                }
            }
            $households = Household::with('individuals')->whereIn('id',$hids)->orderBy('sortsurname','ASC')->get();
            $totpages = ceil(count($households)/12);
            foreach ($households as $household) {
                if ($yy == 40) {
                    $pdf->AddPage('P');
                    $pdf->SetAutoPageBreak(true, 0);
                    $pdf->Image($logopath, 5, 5, 0, 21);
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->SetFont('Arial', 'B', 18);
                    $pdf->text(35, 10, $society->society . " Methodist Church");
                    $pdf->SetFont('Arial', 'B', 14);
                    $pdf->text(35, 24, $group->groupname);
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->text(184, 10, date("d M Y"));
                    $pdf->text(190, 24, "pg " . $pg . " of " . $totpages);
                    $pdf->line(8, 30, 202, 30);
                }
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->text(10, $yy, $household->addressee . ' (' . $household->homephone . ')');
                $yy=$yy+6;
                $msg="";
                foreach ($household->individuals as $ind) {
                    if ($ind->cellphone) {
                        $msg = $msg . $ind->firstname . ' (' . $ind->cellphone . '), ';
                    } else {
                        $msg = $msg . $ind->firstname . ', ';
                    }
                }
                $pdf->SetFont('Arial', '', 10);
                $pdf->text(13, $yy, substr($msg,0,-2));
                $pdf->rect(8, $yy-13, 194, 15);
                $yy=$yy+10;
                if ($yy > 270) {
                    $yy = 40;
                    $pg++;
                }
            }
            $pdf->Output();
        } else {
            $totpages = ceil(count($indivs)/24);
            foreach ($indivs as $indiv) {
                if ($yy == 40) {
                    $pdf->AddPage('P');
                    $pdf->SetAutoPageBreak(true, 0);
                    $pdf->Image($logopath, 5, 5, 0, 21);
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->SetFont('Arial', 'B', 18);
                    $pdf->text(35, 10, $society->society . " Methodist Church");
                    $pdf->SetFont('Arial', 'B', 14);
                    $pdf->text(35, 24, $group->groupname);
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->text(184, 10, date("d M Y"));
                    $pdf->text(190, 24, "pg " . $pg . " of " . $totpages);
                    $pdf->line(8, 30, 202, 30);
                }
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->text(10, $yy, $indiv->surname . ', ' . $indiv->firstname);
                $pdf->SetFont('Arial', '', 12);
                $pdf->text(75, $yy, $indiv->cellphone);
                $pdf->text(105, $yy, $indiv->email);
                $pdf->rect(8, $yy-6, 194, 9);
                $yy=$yy+10;
                if ($yy > 270) {
                    $yy = 40;
                    $pg++;
                }
            }
            $pdf->Output();
        }
        exit;
    }
}
