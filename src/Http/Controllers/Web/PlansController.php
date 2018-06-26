<?php
namespace Bishopm\Churchnet\Http\Controllers\Web;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Auth;
use Bishopm\Churchnet\Libraries\Fpdf\Fpdf;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\Person;
use Bishopm\Churchnet\Repositories\SettingsRepository;
use Bishopm\Churchnet\Repositories\WeekdaysRepository;
use Bishopm\Churchnet\Repositories\MeetingsRepository;
use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Repositories\PeopleRepository;
use Bishopm\Churchnet\Repositories\PlansRepository;
use Bishopm\Churchnet\Repositories\ServicesRepository;
use Bishopm\Churchnet\Repositories\CircuitsRepository;
use Bishopm\Churchnet\Repositories\LabelsRepository;

class PlansController extends Controller
{
    private $settings;
    private $weekdays;
    private $meetings;
    private $societies;
    private $people;
    private $plans;
    private $services;
    private $circuit;
    private $labels;

    public function __construct(
        SettingsRepository $settings,
        WeekdaysRepository $weekdays,
        MeetingsRepository $meetings,
        SocietiesRepository $societies,
        PeopleRepository $people,
        PlansRepository $plans,
        ServicesRepository $services,
        CircuitsRepository $circuit,
        LabelsRepository $labels
    ) {
        $this->settings=$settings;
        $this->weekdays=$weekdays;
        $this->meetings=$meetings;
        $this->societies=$societies;
        $this->people=$people;
        $this->plans=$plans;
        $this->services=$services;
        $this->circuit=$circuit;
        $this->labels=$labels;
    }

    public function plan($slug)
    {
        $this->circuit=$this->circuit->findBySlug($slug);
        $this->settings=$this->settings->allforcircuit($this->circuit->id);
        $one=range(2, 4);
        $two=range(5, 7);
        $three=range(8, 10);
        $four=range(11, 12);
        $m=intval(date('n'));
        $y=intval(date('Y'));
        if (in_array($m, $one)) {
            $this->show($y, 1);
        } elseif (in_array($m, $two)) {
            $this->show($y, 2);
        } elseif (in_array($m, $three)) {
            $this->show($y, 3);
        } elseif (in_array($m, $four)) {
            $this->show($y, 4);
        } elseif ($m==1) {
            $this->show($y-1, 4);
        }
    }

    public function show($yy, $qq)
    {
        $settings=array();
        foreach ($this->settings as $sett) {
            $settings[$sett->setting_key]=$sett->setting_value;
        }
        $data=array();
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
        $data['societies']=$this->societies->allforcircuit($this->circuit->id);
        $data['circuit']=$this->circuit;
        $data['preachers']=Person::where('circuit_id', $this->circuit->id)->where('status', 'preacher')->orderBy('surname')->orderBy('firstname')->get();
        $data['ministers']=Person::withAnyTags(['Circuit minister', 'Superintendent'], 'minister')->where('circuit_id', $this->circuit->id)->where('status', 'minister')->orderBy('surname')->orderBy('firstname')->get();
        $data['supernumeraries']=Person::withAnyTags(['Supernumerary minister'], 'minister')->where('circuit_id', $this->circuit->id)->where('status', 'minister')->orderBy('surname')->orderBy('firstname')->get();
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
        $pm1=$this->plans->sqlQuery("SELECT plans.*,people.firstname,people.surname,positions.* from people,person_position,positions,plans LEFT JOIN preachers ON plans.preacher_id=preachers.id WHERE planyear = '" . $y1 . "' and planmonth ='" . $m1 . "' and preachers.person_id = people.id and person_position.person_id = people.id and person_position.position_id = positions.id and positions.selectgroup = 1");
        foreach ($pm1 as $p1) {
            $soc=$this->societies->find($p1->society_id)->society;
            $ser=$this->services->find($p1->service_id)->servicetime;
            if ($p1->position=="Circuit minister") {
                $p1typ="M_";
            } elseif ($p1->position=="Guest") {
                $p1typ="G_";
            } else {
                $p1typ="P_";
            }
            if ($p1->preacher_id) {
                $data['fin'][$soc][$p1->planyear][$p1->planmonth][$p1->planday][$ser]['preacher']=$p1typ . $p1->preacher_id;
                $data['fin'][$soc][$p1->planyear][$p1->planmonth][$p1->planday][$ser]['pname']=substr($p1->firstname, 0, 1) . " " . $p1->surname;
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
        $pm2=$this->plans->sqlQuery("SELECT plans.*,people.firstname,people.surname,positions.* from people,person_position,positions,plans LEFT JOIN preachers ON plans.preacher_id=preachers.id WHERE planyear = '" . $y2 . "' and planmonth ='" . $m2 . "' and preachers.person_id = people.id and person_position.person_id = people.id and person_position.position_id = positions.id and positions.selectgroup = 1");
        foreach ($pm2 as $p2) {
            $soc=$this->societies->find($p2->society_id)->society;
            $ser=$this->services->find($p2->service_id)->servicetime;
            if ($p2->position=="Circuit minister") {
                $p2typ="M_";
            } elseif ($p2->position=="Guest") {
                $p2typ="G_";
            } else {
                $p2typ="P_";
            }
            if ($p2->preacher_id) {
                $data['fin'][$soc][$p2->planyear][$p2->planmonth][$p2->planday][$ser]['preacher']=$p2typ . $p2->preacher_id;
                $data['fin'][$soc][$p2->planyear][$p2->planmonth][$p2->planday][$ser]['pname']=substr($p2->firstname, 0, 1) . " " . $p2->surname;
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
        $pm3=$this->plans->sqlQuery("SELECT plans.*,people.firstname,people.surname,positions.* from people,person_position,positions,plans LEFT JOIN preachers ON plans.preacher_id=preachers.id WHERE planyear = '" . $y3 . "' and planmonth ='" . $m3 . "' and preachers.person_id = people.id and person_position.person_id = people.id and person_position.position_id = positions.id and positions.selectgroup = 1");
        foreach ($pm3 as $p3) {
            $soc=$this->societies->find($p3->society_id)->society;
            $ser=$this->services->find($p3->service_id)->servicetime;
            if ($p3->position=="Circuit minister") {
                $p3typ="M_";
            } elseif ($p3->position=="Guest") {
                $p3typ="G_";
            } else {
                $p3typ="P_";
            }
            if ($p3->preacher_id) {
                $data['fin'][$soc][$p3->planyear][$p3->planmonth][$p3->planday][$ser]['preacher']=$p3typ . $p3->preacher_id;
                $data['fin'][$soc][$p3->planyear][$p3->planmonth][$p3->planday][$ser]['pname']=substr($p3->firstname, 0, 1) . " " . $p3->surname;
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
        $y_add=($pg_height-$header-3*($num_ser-$num_soc))/$num_ser;
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
        foreach ($dat['societies'] as $soc) {
            $firstserv=true;
            foreach ($soc['services'] as $ser) {
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
                            $pdf->cell($x_add, $y_add-6, $wd->description, 0, 0, 'C');
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
                        $trial=$this->preachers->find($dat['fin'][$soc['society']][$sun['yy']][$sun['mm']][$sun['dd']][$ser['servicetime']]['trial']);
                        $tname="[" . utf8_decode(substr($trial->firstname, 0, 1) . " " . $trial->surname) . "]";
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
            $pdf->line($x2, $header+$y_add-2, $x2, $y+$y_add-2);
            $x2=$x2+$x_add;
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
            $thissoc=$this->societies->find($preacher1->society_id)->society;
            $dum['name']=$preacher1->title . " " . $preacher1->firstname . " " . $preacher1->surname;
            if ($preacher1->position=="Emeritus preacher") {
                $dum['name'] = $dum['name'] . "*";
            }
            $dum['soc']=$preacher1->society_id;
            $dum['cellphone']=$preacher1->phone;
            $dum['fullplan']=$preacher1->fullplan;
            $dum['position']=$preacher1->position;
            if ($dum['fullplan']=="Trial") {
                $vdum['9999' . $preacher1->surname . $preacher1->firstname]=$dum;
            } else {
                $vdum[$preacher1->fullplan . $preacher1->surname . $preacher1->firstname]=$dum;
            }
        }
        ksort($vdum);
        foreach ($vdum as $vd) {
            $thissoc=$this->societies->find($vd['soc'])->society;
            $pfin[$thissoc][]=$vd;
        }
        $cols=4;
        $spacer=5;
        $col_width=($pg_width-(2*$left_side))/$cols;
        $y=30;
        $col=1;
        $pdf->SetFont('Arial', '', 8);
        $pdf->text($left_side+$spacer, $y, "Presiding Bishop: " . $settings['presiding_bishop']);
        $y=$y+4;
        $pdf->text($left_side+$spacer, $y, "General Secretary: " . $settings['general_secretary']);
        $y=$y+4;
        $pdf->text($left_side+$spacer, $y, "District Bishop: " . $settings['district_bishop']);
        $y=$y+6;
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->text($left_side+$spacer, $y, "Circuit Ministers");
        $y=$y+4;
        $pdf->SetFont('Arial', '', 8);
        foreach ($dat['ministers'] as $min) {
            if ($min->position == "Circuit minister") {
                $super = "";
            } else {
                $super = " [Supt]";
            }
            $pdf->text($left_side+$spacer, $y, $min->title . " " . substr($min->firstname, 0, 1) . " " . $min->surname . " (" . $min->phone . ")" . $super);
            $y=$y+4;
        }
        if (isset($dat['supernumeraries'])) {
            $y=$y+2;
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->text($left_side+$spacer, $y, "Supernumerary Ministers");
            $y=$y+4;
            $pdf->SetFont('Arial', '', 8);
            foreach ($dat['supernumeraries'] as $supm) {
                $pdf->text($left_side+$spacer, $y, $supm->title . " " . substr($supm->firstname, 0, 1) . " " . $supm->surname . " (" . $supm->phone . ")");
                $y=$y+4;
            }
        }
        $y=$y+2;
        $pdf->SetFont('Arial', '', 8);
        $officers=$this->positions->identify($this->circuit->id, 'Circuit steward');
        $subhead="";
        if ($officers) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->text($left_side+$spacer, $y, "Circuit Stewards");
            $pdf->SetFont('Arial', '', 8);
            foreach ($officers as $officer) {
                $y=$y+4;
                $pdf->text($left_side+$spacer, $y, $officer);
            }
        }
        $pdf->SetFont('Arial', 'B', 11);
        $y=$y+6;
        $treasurer=$this->positions->identify($this->circuit->id, 'Circuit treasurer')[0];
        if ($treasurer) {
            $pdf->text($left_side+$spacer, $y, "Circuit Treasurer");
            $pdf->SetFont('Arial', '', 8);
            $y=$y+4;
            $pdf->text($left_side+$spacer, $y, $treasurer);
            $pdf->SetFont('Arial', 'B', 11);
            $y=$y+6;
        }
        $csecretary=$this->positions->identify($this->circuit->id, 'Circuit secretary')[0];
        if ($csecretary) {
            $pdf->text($left_side+$spacer, $y, "Circuit Secretary");
            $pdf->SetFont('Arial', '', 8);
            $y=$y+4;
            $pdf->text($left_side+$spacer, $y, $csecretary);
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
        $supervisor=$this->positions->identify($this->circuit->id, 'Circuit supervisor of studies')[0];
        if ($supervisor) {
            $y=$y+4;
            $pdf->SetFont('Arial', '', 8);
            $pdf->text($x, $y, "Supervisor of studies: " . $supervisor);
        }
        $lpsec=$this->positions->identify($this->circuit->id, 'Local preachers secretary')[0];
        if ($lpsec) {
            $y=$y+4;
            $pdf->SetFont('Arial', '', 8);
            $pdf->text($x, $y, "Local Preachers Secretary: " . $lpsec);
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
                if (($pre['position']=="Local preacher") or ($pre['position']=="On trial preacher") or ($pre['position']=="Emeritus preacher")) {
                    $pdf->text($x+2, $y, $pre['fullplan']);
                    $pdf->text($x+10, $y, $pre['name'] . " (" . $pre['cellphone'] . ")");
                    $y=$y+4;
                }
            }
        }
        $pdf->SetFont('Arial', '', 8);
        $y=$y+4;
        $pdf->text($x+2, $y, "* Emeritus");
        $pdf->Output();
        exit;
    }
}
