<?php

namespace Bishopm\Churchnet\Console;

use Illuminate\Console\Command;
use Bishopm\Churchnet\Models\Book;
use Bishopm\Churchnet\Models\Payment;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Setting;
use DB;
use Bishopm\Churchnet\Mail\GivingMail;
use Bishopm\Churchnet\Mail\SimpleMail;
use Illuminate\Support\Facades\Mail;

class PlannedGivingReportEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'churchnet:givingemails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send giving report by email to planned givers';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today=date('Y-m-d');
        $lagtime=intval(Setting::where('setting_key', 'giving_lagtime')->first()->setting_value);
        //echo "You have a lag setting of " . $lagtime . " days\n";
        $effdate=strtotime($today)-$lagtime*86400;
        //echo "Effdate: " . date("d M Y", $effdate) . "\n";
        $repyr=date('Y', $effdate);
        //echo "Your report year is " . $repyr . "\n";
        $reportnums=intval(Setting::where('setting_key', 'giving_reports')->first()->setting_value);
        //echo "Your system will deliver " . $reportnums . " reports per year\n";
        $adminuser=Setting::where('setting_key', 'giving_administrator')->first()->setting_value;
        $administrator=User::find($adminuser)->individual->email;
        switch ($reportnums) {
            case 1:
                $reportdates=array($repyr . "-12-31");
                break;
            case 2:
                $reportdates=array($repyr . "-06-30",$repyr . "-12-31");
                break;
            case 3:
                $reportdates=array($repyr . "-04-30",$repyr . "-08-31",$repyr . "-12-31");
                break;
            case 4:
                $reportdates=array($repyr . "-03-31",$repyr . "-06-30",$repyr . "-09-30",$repyr . "-12-31");
                break;
            case 6:
                $reportdates=array($repyr . "-02-28",$repyr . "-04-30",$repyr . "-06-30",$repyr . "-08-31",$repyr . "-10-31",$repyr . "-12-31");
                break;
            case 12:
                $reportdates=array($repyr . "-01-31",$repyr . "-02-28",$repyr . "-03-31",$repyr . "-04-30",$repyr . "-05-31",$repyr . "-06-30",$repyr . "-07-31",$repyr . "-08-31",$repyr . "-09-30",$repyr . "-10-31",$repyr . "-11-30",$repyr . "-12-31");
                break;
        }
        if (in_array(date("Y-m-d", $effdate), $reportdates)) {
            $period=12/$reportnums;
            $endofperiod=date('Y-m-t', $effdate);
            $startmonth=intval(date('m', $effdate))-$period+1;
            if ($startmonth<1) {
                $startmonth=$startmonth+12;
            }
            if ($startmonth<10) {
                $sm="0" . strval($startmonth);
            } else {
                $sm=strval($startmnonth);
            }
            $startofperiod=$repyr . "-" . $sm . "-01";
            //echo "Calculating totals for the period: " . $startofperiod . " to " . $endofperiod . "\n";
            $givers=Individual::where('giving', '>', 0)->where('email', '<>', '')->get();
            $noemailgivers=Individual::where('giving', '>', 0)->where('email', '')->get();
            $msg="Planned giving emails were sent today to " . count($givers) . " planned givers.";
            if (count($noemailgivers)) {
                $msg.="<br><br>The following planned givers do not have email addresses and require a hardcopy report:<br><br>";
                foreach ($noemailgivers as $nomail) {
                    $msg.=$nomail->firstname . " " . $nomail->surname . "<br>";
                }
            } else {
                $msg.="<br><br>Good news! All planned givers at present have email addresses :)";
            }
            $msg.="<br><br>Thank you!";
            $nodat=new \stdClass();
            $nodat->subject="Planned giving emails sent";
            $nodat->sender="info@umc.org.za";
            $nodat->emailmessage=$msg;
            Mail::to($administrator)->send(new SimpleMail($nodat));
            foreach ($givers as $giver) {
                $data[$giver->giving]['email'][]=$giver->email;
                if (count($data[$giver->giving]['email'])==1) {
                    $data[$giver->giving]['period']=$startofperiod . " to " . $endofperiod;
                    $data[$giver->giving]['sender']=Setting::where('setting_key', 'church_email')->first()->setting_value;
                    $data[$giver->giving]['pg']=$giver->giving;
                    $data[$giver->giving]['pgyr']=$repyr;
                    $data[$giver->giving]['church']=Setting::where('setting_key', 'site_name')->first()->setting_value;
                    $data[$giver->giving]['churchabbr']=Setting::where('setting_key', 'site_abbreviation')->first()->setting_value;
                    if ($period==1) {
                        $data[$giver->giving]['scope']="month";
                    } else {
                        $data[$giver->giving]['scope']=$period . " months";
                    }
                    $data[$giver->giving]['subject']="Planned giving feedback: " . $startofperiod . " to " . $endofperiod;
                    $currentpayments=Payment::where('pgnumber', $giver->giving)->where('paymentdate', '>=', $startofperiod)->where('paymentdate', '<=', $endofperiod)->orderBy('paymentdate', 'DESC')->get();
                    foreach ($currentpayments as $cp) {
                        $data[$giver->giving]['current'][]=$cp;
                    }
                    $historicpayments=Payment::where('pgnumber', $giver->giving)->where('paymentdate', '<', $startofperiod)->where('paymentdate', '>=', $repyr . '-01-01')->orderBy('paymentdate', 'DESC')->get();
                    foreach ($historicpayments as $hp) {
                        $data[$giver->giving]['historic'][]=$hp;
                    }
                }
            }
            foreach ($data as $key=>$pg) {
                foreach ($pg['email'] as $indiv) {
                    Mail::to($indiv)->send(new GivingMail($pg));
                }
            }
        } else {
            $warningdate=date("Y-m-d", $effdate+432000);
            if (in_array($warningdate, $reportdates)) {
                $msg="This is a reminder that your system is configured to send out planned giving emails in 5 days time for the " . 12/$reportnums . " month period ending: " . $warningdate;
                $msg.=".<br><br>If there are any payments for that period that have not yet been captured, you can still add them to the system and they will ";
                $msg.="be included, provided the date of receipt falls within the period being reported.<br><br>Thank you!";
                $warndat=new \stdClass();
                $warndat->subject="Planned giving reminder";
                $warndat->sender="info@umc.org.za";
                $warndat->emailmessage=$msg;
                Mail::to($administrator)->send(new SimpleMail($warndat));
            } else {
                //echo "Today is not a report date\n";
            }
        }
    }
}
