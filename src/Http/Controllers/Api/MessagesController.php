<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Mail\GenericMail;
use Bishopm\Churchnet\Models\Group;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Events\MessagePosted;
use Illuminate\Support\Facades\Mail;
use Pusher\Pusher;
use Carbon\Carbon;
use Swift_SmtpTransport;
use Illuminate\Support\Facades\DB;
use Bishopm\Churchnet\Libraries\SMSfunctions;
use Illuminate\Http\Request;

class MessagesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function send(Request $request)
    {
        $data = $request->message;
        $recipients=$this->getrecipients($data['groups'], $data['individuals'], "", $data['messagetype']);
        if ($data['messagetype']=="email") {
            return $this->sendemail($data, $recipients);
        } elseif ($data['messagetype']=="sms") {
            return $this->sendsms($data['textmessage'], $recipients, $data['society_id']);
        } elseif ($data['messagetype']=="app") {
            $sender=Auth::user()->id;
            foreach ($recipients as $key=>$rec) {
                $msg = $this->sendmessage($sender, $key, $data['emailmessage']);
            }
        }
    }

    public function sendmessage($sender, $receiver, $message)
    {
        $this->messages->create(['user_id'=>$sender, 'receiver_id'=>$receiver, 'message'=>$message, 'viewed'=>0]);
        $this->pusher->trigger('messages', 'new_message', $message);
    }

    public function api_usermessages($id)
    {
        $messages = DB::select('SELECT m1.*, individuals.*, m1.created_at as m1c  FROM users,individuals,messages m1 LEFT JOIN messages m2 ON (m1.user_id = m2.user_id AND m1.created_at < m2.created_at) WHERE m1.user_id=users.id and users.individual_id=individuals.id and m2.created_at IS NULL and m1.receiver_id = ? order by m1.created_at DESC', [$id]);
        $newmsg=0;
        foreach ($messages as $message) {
            $message->ago = Carbon::parse($message->m1c)->diffForHumans();
            if ($message->viewed == 0) {
                $newmsg++;
            }
        }
        $messages['newmsg']=$newmsg;
        return $messages;
    }

    public function api_messagethread($user, $id)
    {
        return $this->messages->thread($user, $id);
    }

    public function apisendmessage(Request $request)
    {
        $message = $this->messages->create(['user_id'=>$request->user_id, 'receiver_id'=>$request->receiver_id, 'message'=>$request->message, 'viewed'=>0]);
        $this->pusher->trigger('messages', 'new_message', $message);
    }

    protected function getrecipients($groups, $individuals, $grouprec, $msgtype)
    {
        $recipients=array();
        if ($grouprec=="allchurchmembers") {
            $indivs=$this->individuals->allchurchmembers();
            foreach ($indivs as $indiv) {
                if (((null !== $indiv->household->householdcell) and ($indiv->household->householdcell==$indiv->id)) or ($msgtype=="email")) {
                    if ((($msgtype=="email") and ($indiv->email)) or (($msgtype=="sms") and ($indiv->cellphone))) {
                        $recipients[$indiv->household_id][$indiv->id]['name']=$indiv->fullname;
                        $recipients[$indiv->household_id][$indiv->id]['email']=$indiv->email;
                        $recipients[$indiv->household_id][$indiv->id]['cellphone']=$indiv->cellphone;
                    }
                }
            }
        } elseif ($grouprec=="everyone") {
            $indivs=$this->individuals->everyone();
            foreach ($indivs as $indiv) {
                if (((null !== $indiv->household->householdcell) and ($indiv->household->householdcell==$indiv->id)) or ($msgtype=="email")) {
                    if ((($msgtype=="email") and ($indiv->email)) or (($msgtype=="sms") and ($indiv->cellphone))) {
                        $recipients[$indiv->household_id][$indiv->id]['name']=$indiv->fullname;
                        $recipients[$indiv->household_id][$indiv->id]['email']=$indiv->email;
                        $recipients[$indiv->household_id][$indiv->id]['cellphone']=$indiv->cellphone;
                    }
                }
            }
        } else {
            if (null!==$groups) {
                foreach ($groups as $group) {
                    $indivs=Group::find($group)->individuals;
                    foreach ($indivs as $indiv) {
                        $recipients[$indiv->household_id][$indiv->id]['name']=$indiv->fullname;
                        $recipients[$indiv->household_id][$indiv->id]['email']=$indiv->email;
                        $recipients[$indiv->household_id][$indiv->id]['cellphone']=$indiv->cellphone;
                    }
                }
            }
            if (null!==$individuals) {
                foreach ($individuals as $indiv) {
                    $indi=$this->individuals->find($indiv);
                    $recipients[$indi->household_id][$indi->id]['name']=$indi->fullname;
                    $recipients[$indi->household_id][$indi->id]['email']=$indi->email;
                    $recipients[$indi->household_id][$indi->id]['cellphone']=$indi->cellphone;
                }
            }
        }
        return $recipients;
    }

    protected function sendemail($data, $recipients)
    {
        $results=array();
        $sender = $data['sender'];
        $sendertold=false;
        $settings = Society::find($data['society_id']);
        foreach ($recipients as $household) {
            foreach ($household as $indiv) {
                $dum['name']=$indiv['name'];
                $dum['address']=$indiv['email'];
                if ($sender==$indiv['email']) {
                    $sendertold=true;
                }
                if (filter_var($indiv['email'], FILTER_VALIDATE_EMAIL)) {
                    $transport = (new Swift_SmtpTransport($settings->email_host, $settings->email_port))
                       ->setUsername($settings->email_user)
                       ->setPassword($settings->email_pw)
                       ->setEncryption($settings->email_encryption);
                    Mail::setSwiftMailer(new \Swift_Mailer($transport));
                    Mail::to($indiv['email'])->send(new GenericMail($data));
                    $dum['emailresult']="OK";
                } else {
                    $dum['emailresult']="Invalid";
                }
                $results[]=$dum;
            }
        }
        // Send a copy to the sender
        if (!$sendertold) {
            Mail::to($sender)->send(new GenericMail($data));
        }
        return $results;
    }

    public function sendsms($message, $recipients, $soc)
    {
        $society = Society::find($soc);
        $credits=SMSfunctions::BS_get_credits($society['bulksms_user'], $society['bulksms_pw']);
        $url = 'http://community.bulksms.com/eapi/submission/send_sms/2/2.0';
        $port = 80;
        if (count($recipients)>$credits) {
            return "Insufficient Bulk SMS credits to send SMS";
        }
        foreach ($recipients as $household) {
            foreach ($household as $sms) {
                $seven_bit_msg=$message;
                $transient_errors = array(40 => 1);
                $msisdn = "+27" . substr($sms['cellphone'], 1);
                $post_body = SMSfunctions::BS_seven_bit_sms($society['bulksms_user'], $society['bulksms_pw'], $seven_bit_msg, $msisdn);
                $dum2['name']=$sms['name'];
                if (SMSfunctions::checkcell($sms['cellphone'])) {
                    $dum2['smsresult'] = SMSfunctions::BS_send_message($post_body, $url, $port);
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
        return $data['results'];
    }
}
