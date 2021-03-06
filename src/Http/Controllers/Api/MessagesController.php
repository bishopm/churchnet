<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Bishopm\Churchnet\Mail\GenericMail;
use Bishopm\Churchnet\Models\Group;
use Bishopm\Churchnet\Models\Household;
use Bishopm\Churchnet\Models\Society;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Bishopm\Churchnet\Services\BulkSMSService;
use Bishopm\Churchnet\Services\SMSPortalService;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Jobs\DeliverSMS;

class MessagesController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function send(Request $request)
    {
        $data = $request->message;
        $recipients = $this->getrecipients($data['groups'], $data['individuals'], $data['society_id'], $data['messagetype']);
        if ($data['messagetype'] == "email") {
            return $this->sendemail($data, $recipients);
        } elseif ($data['messagetype'] == "sms") {
            return $this->sendsms($data['textmessage'], $recipients, $data['society_id']);
        }
    }

    public function api_usermessages($id)
    {
        $messages = DB::select('SELECT m1.*, individuals.*, m1.created_at as m1c  FROM users,individuals,messages m1 LEFT JOIN messages m2 ON (m1.user_id = m2.user_id AND m1.created_at < m2.created_at) WHERE m1.user_id=users.id and users.individual_id=individuals.id and m2.created_at IS NULL and m1.receiver_id = ? order by m1.created_at DESC', [$id]);
        $newmsg = 0;
        foreach ($messages as $message) {
            $message->ago = Carbon::parse($message->m1c)->diffForHumans();
            if ($message->viewed == 0) {
                $newmsg++;
            }
        }
        $messages['newmsg'] = $newmsg;
        return $messages;
    }

    public function api_messagethread($user, $id)
    {
        return $this->messages->thread($user, $id);
    }

    public function apisendmessage(Request $request)
    {
        $message = $this->messages->create(['user_id' => $request->user_id, 'receiver_id' => $request->receiver_id, 'message' => $request->message, 'viewed' => 0]);
        $this->pusher->trigger('messages', 'new_message', $message);
    }

    protected function getrecipients($groups, $individuals, $society, $msgtype)
    {
        $recipients = array();
        if (in_array(0, $groups)) {
            $households = Household::members($society);
            foreach ($households as $household) {
                foreach ($household->individuals as $indiv) {
                    if ($msgtype === "sms") {
                        if ((null !== $household->householdcell) and ($household->householdcell == $indiv->id)) {
                            if ($indiv->cellphone) {
                                $recipients[$indiv->household_id][$indiv->id]['name'] = $indiv->fullname;
                                if ($indiv->email) {
                                    $recipients[$indiv->household_id][$indiv->id]['email'] = $indiv->email;
                                }
                                $recipients[$indiv->household_id][$indiv->id]['cellphone'] = $indiv->cellphone;
                            }
                        }
                    } else {
                        if ($indiv->email) {
                            $recipients[$indiv->household_id][$indiv->id]['name'] = $indiv->fullname;
                            $recipients[$indiv->household_id][$indiv->id]['email'] = $indiv->email;
                            if ($indiv->cellphone) {
                                $recipients[$indiv->household_id][$indiv->id]['cellphone'] = $indiv->cellphone;
                            }
                        }
                    }
                }
            }
        } else {
            if (null !== $groups) {
                foreach ($groups as $group) {
                    $indivs = Group::find($group)->individuals;
                    foreach ($indivs as $indiv) {
                        $recipients[$indiv->household_id][$indiv->id]['name'] = $indiv->fullname;
                        $recipients[$indiv->household_id][$indiv->id]['email'] = $indiv->email;
                        $recipients[$indiv->household_id][$indiv->id]['cellphone'] = $indiv->cellphone;
                    }
                }
            }
            if (null !== $individuals) {
                foreach ($individuals as $indiv) {
                    $indi = $this->individuals->find($indiv);
                    $recipients[$indi->household_id][$indi->id]['name'] = $indi->fullname;
                    $recipients[$indi->household_id][$indi->id]['email'] = $indi->email;
                    $recipients[$indi->household_id][$indi->id]['cellphone'] = $indi->cellphone;
                }
            }
        }
        return $recipients;
    }

    protected function sendemail($data, $recipients)
    {
        $results = array();
        $sender = $data['sender'];
        $sendertold = false;
        $settings = Society::find($data['society_id']);
        $data['society'] = $settings->society;
        $data['website'] = $settings->website;
        foreach ($recipients as $household) {
            foreach ($household as $indiv) {
                $dum['name'] = $indiv['name'];
                $dum['address'] = $indiv['email'];
                if ($sender == $indiv['email']) {
                    $sendertold = true;
                }
                if (filter_var($indiv['email'], FILTER_VALIDATE_EMAIL)) {
                    if (!isset($data['attachment']['data'])){
                        Mail::to($indiv['email'])->queue(new GenericMail($data));
                    } else {
                        // Attachments can't be queued
                        Mail::to($indiv['email'])->send(new GenericMail($data));
                    }
                    $dum['emailresult'] = "OK";
                } else {
                    $dum['emailresult'] = "Invalid";
                }
                $results[] = $dum;
            }
        }
        // Send a copy to the sender
        if (!$sendertold) {
            Mail::to($sender)->queue(new GenericMail($data));
        }
        return $results;
    }

    private function checkcell($cell)
    {
        if (strlen($cell) !== 10) {
            return false;
        } else {
            if (preg_match("/^[0-9]+$/", $cell)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function sendsms($message, $recipients, $soc)
    {
        $society = Society::find($soc);
        if ($society['sms_service'] == 'bulksms') {
            $smss = new BulkSMSService($society['sms_user'], $society['sms_pw']);
        } elseif ($society['sms_service'] == 'smsportal') {
            $smss = new SMSPortalService($society['sms_user'], $society['sms_pw']);
        }
        $credits = $smss->get_credits($society['sms_user'], $society['sms_pw']);
        if (count($recipients) > $credits) {
            return "Insufficient SMS credits to send SMS";
        }
        $messages = array();
        foreach ($recipients as $household) {
            foreach ($household as $sms) {
                $msisdn = "+27" . substr($sms['cellphone'], 1);
                if ($this->checkcell($sms['cellphone'])) {
                    if ($society['sms_service'] == 'bulksms') {
                        $messages[] = array('to' => $msisdn, 'body' => $message);
                    } elseif ($society['sms_service'] == 'smsportal') {
                        $messages[] = array('Destination' => $msisdn, 'Content' => $message);
                    }
                }
            }
        }
        $data['results'] = DeliverSMS::dispatch($messages, $smss);
        $data['type'] = "SMS";
        return $data;
    }

    public function getsmscredits(Request $request)
    {
        $society = Society::find($request->society);
        if ($society['sms_service'] == 'bulksms') {
            $smss = new BulkSMSService($society['sms_user'], $society['sms_pw']);
        } elseif ($society['sms_service'] == 'smsportal') {
            $smss = new SMSPortalService($society['sms_user'], $society['sms_pw']);
        }
        return $smss->get_credits();
    }
}
