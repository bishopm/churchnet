<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Models\Roster;
use Bishopm\Churchnet\Models\Rosteritem;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Group;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\User;
use DB;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Libraries\SMSfunctions;
use Bishopm\Churchnet\Libraries\Fpdf\Fpdf;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;

class RostersController extends Controller
{
    private function _get_week_dates($yy, $mm, $dd)
    {
        $mm = date_parse($mm)['month'];
        $ddn = date('N', strtotime($dd));
        if (strlen($mm)==1) {
            $mm="0" . $mm;
        }
        $firstdayofmonth=$yy . "-" . $mm . "-" . "01";
        if ((date("w", strtotime($firstdayofmonth))==$ddn) or (date("w", strtotime($firstdayofmonth))==$ddn-7)) {
            $weeks[0]=$firstdayofmonth;
        } else {
            $weeks[0]=date("Y-m-d", strtotime($dd, strtotime($firstdayofmonth)));
        }
        $weeks[1]=date("Y-m-d", strtotime($weeks[0])+604800);
        $weeks[2]=date("Y-m-d", strtotime($weeks[1])+604800);
        $weeks[3]=date("Y-m-d", strtotime($weeks[2])+604800);
        if (date("m", strtotime($weeks[3])+604800)==date("m", strtotime($weeks[0]))) {
            $weeks[4]=date("Y-m-d", strtotime($weeks[3])+604800);
        }
        return $weeks;
    }

    public function currentroster($rosterid)
    {
        $this->report($rosterid, date('Y'), date('m'));
    }

    public function nextroster($rosterid)
    {
        $this->report($rosterid, date('Y'), 1+intval(date('m')));
    }

    public function report($rosterid, $yy, $mm)
    {
        $pdf = new Fpdf();
        $pdf->AddPage('L');
        $logopath=base_path() . '/public/images/logo.jpg';
        $roster = Roster::with('rostergroups')->find($rosterid);
        $weeks=self::_get_week_dates($yy, $mm, $roster->dayofweek);
        $churchname=$roster->society->society . ' Methodist Church';
        $x=15;
        $pdf->SetAutoPageBreak(0, 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTitle($churchname . " - " . $roster->name);

        // SET UP DATA STRUCTURE
        DB::enableQueryLog();
        foreach ($weeks as $wkno=>$week) {
            foreach ($roster->rostergroups as $grp) {
                $ri = Rosteritem::with('individuals')->where('rostergroup_id', $grp->id)->where('rosterdate', $week)->first();
                $data[$grp->group->groupname][$week]=$ri['individuals'];
            }
            ksort($data);
        }
        $title=$churchname . ": " . $roster->name. " (" . date("F", strtotime($weeks[0])) . " " . $yy . ")";
        $pdf->setxy(10, 8);
        $pdf->cell(0, 0, $title, 0, 0, 'C');
        $first=true;
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(215, 215, 215);
        $rcount=0;
        $headings=array();
        foreach ($data as $gg=>$row) {
            $yy=$rcount*6+22;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->setxy(10, $yy);
            $pdf->cell(0, 0, $gg, 0, 0);
            $xx=60;
            $maxi=1;
            foreach ($row as $wk=>$cell) {
                $pdf->SetFont('Arial', '', 10);
                if (!in_array(date("j F Y", strtotime($wk)),$headings)){
                    $headings[] = date("j F Y", strtotime($wk));
                }
                $yadd=0;
                if ($cell) {
                    foreach ($cell as $ii) {
                        if (count($cell)>$maxi){
                            $maxi=count($cell);
                        }
                        $pdf->setxy($xx, $yy+$yadd);
                        if ($ii) {
                            $pdf->cell(45, 0, $ii['firstname'] . ' ' . $ii['surname'], 0, 0, 'C');
                        }
                        $yadd=$yadd+4;
                    }
                }
                $xx=$xx+45;
            }
            $pdf->rect(10,$yy-3,278,1+($maxi*5));
            $rcount=$rcount+$maxi;
        }
        $pdf->SetFont('Arial', 'B', 10);
        $xx=60;
        foreach ($headings as $heading){
            if (($xx==105) or ($xx==195)){
                $pdf->rect($xx,12,45,$yy-14+($maxi*5));
            }
            $pdf->setxy($xx, 16);
            $pdf->cell(45, 0, $heading, 0, 0, 'C');
            $xx=$xx+45;
        }
        $pdf->Output();
        exit;
    }

    public function sms($id, $send, Request $request)
    {
        $settings=$this->settings->makearray();
        $extra=array();
        $extra=$request->extrainfo;
        $data['extrainfo']=$extra;
        $daysofweek=array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
        $data['roster'] = Roster::with(array('group'))->find($id);
        $data['rosterday']=$daysofweek[$data['roster']->dayofweek-1];
        $dday=date("Y-m-d", strtotime('next ' . $data['rosterday']));
        $rosterdetails=DB::table('group_individual_roster')->join('groups', 'group_id', '=', 'groups.id')->join('individuals', 'individual_id', '=', 'individuals.id')->join('rosters', 'roster_id', '=', 'rosters.id')->select('surname', 'firstname', 'cellphone', 'groupname', 'message', 'dayofweek', 'group_id', 'household_id')->where('rosterdate', '=', $dday)->where('roster_id', '=', $id)->orderby('groupname')->get();
        foreach ($rosterdetails as $detail) {
            $dum['cellphone']=$detail->cellphone;
            $dum['message']=$detail->message . " (" . $detail->groupname . ")";
            $dum['household']=$detail->household_id;
            if (($extra) and (array_key_exists($detail->group_id, $extra))) {
                $dum['message']=$dum['message'] . " (" . $extra[$detail->group_id] . ")";
            }
            if (strpos($dum['message'], "[dayofweek]")) {
                $dum['message']=str_replace("[dayofweek]", $data['rosterday'], $dum['message']);
            }
            if (strpos($dum['message'], "[groupname]")) {
                $dum['message']=str_replace("[groupname]", $detail->groupname, $dum['message']);
            }
            if (strpos($dum['message'], "[firstname]")) {
                $dum['message']=str_replace("[firstname]", $detail->firstname, $dum['message']);
            }
            $dum['recipient']=$detail->firstname . " " . $detail->surname;
            $data['rosterdetails'][]=$dum;
        }
        $data['rosterdate']=$dday;
        if ($settings['sms_provider']=="bulksms") {
            $data['credits']=SMSfunctions::BS_get_credits($settings['sms_username'], $settings['sms_password']);
        } else {
            $data['credits']=SMSfunctions::SF_checkCredits($settings['sms_username'], $settings['sms_password']);
        }
        if ($send=="preview") {
            return View::make('churchnet::rosters.sms', $data);
        } else {
            if ($settings['sms_provider']=="bulksms") {
                if (count($data['rosterdetails'])>SMSfunctions::BS_get_credits($settings['sms_username'], $settings['sms_password'])) {
                    return Redirect::back()->withInput()->withErrors("Insufficient Bulk SMS credits to send SMS");
                }
                $url = 'http://community.bulksms.com/eapi/submission/send_sms/2/2.0';
                $port = 80;
            } elseif ($settings['sms_provider']=="smsfactory") {
                if (count($data['rosterdetails'])>SMSfunctions::SF_checkCredits($settings['sms_username'], $settings['sms_password'])) {
                    return Redirect::back()->withInput()->withErrors("Insufficient SMS Factory credits to send SMS");
                }
            }
            foreach ($data['rosterdetails'] as $sms) {
                $seven_bit_msg=$sms['message'] . " (From " . $settings['site_abbreviation'] . ")";
                if ($settings['sms_provider']=="bulksms") {
                    $transient_errors = array(40 => 1);
                    $msisdn = "+27" . substr($sms['cellphone'], 1);
                    $post_body = SMSfunctions::BS_seven_bit_sms($settings['sms_username'], $settings['sms_password'], $seven_bit_msg, $msisdn);
                }
                $dum2['name']=$sms['recipient'];
                $dum2['household']=$sms['household'];
                if (SMSfunctions::checkcell($sms['cellphone'])) {
                    if ($settings['sms_provider']=="bulksms") {
                        $smsresult = SMSfunctions::BS_send_message($post_body, $url, $port);
                    } elseif ($settings['sms_provider']=="smsfactory") {
                        $smsresult = SMSfunctions::SF_sendSms($settings['sms_username'], $settings['sms_password'], $sms['cellphone'], $seven_bit_msg);
                    }
                    $dum2['address']=$sms['cellphone'];
                } else {
                    if ($sms['cellphone']=="") {
                        $dum2['address']="No cell number provided.";
                    } else {
                        $dum2['address']="Invalid cell number: " . $sms['cellphone'] . ".";
                    }
                }
                $results[]=$dum2;
            }
            $data['results']=$results;
            $data['type']="SMS";
        }
        return View::make('churchnet::rosters.results', $data);
    }
}
