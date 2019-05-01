<?php

namespace Bishopm\Churchnet\Console;

use Illuminate\Console\Command;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Plan;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Reminder;
use Bishopm\Churchnet\Models\Household;
use DB;
use Log;
use Bishopm\Churchnet\Notifications\PushNotification;
use Illuminate\Support\Facades\Notification;
use Bishopm\Churchnet\Http\Controllers\Api\LectionaryController;

class PreacherReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'churchnet:preacherreminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a weekly preaching reminder to preachers';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $nextsunday = date('Y-m-d', strtotime('Next Sunday'));
        $readings = app(LectionaryController::class)->sunday($nextsunday);
        $sunday = explode('-', date('Y-n-j', strtotime('Next Sunday')));
        $plans = Plan::with('service.society', 'person.individual.user')->where('planyear', $sunday[0])->where('planmonth', $sunday[1])->where('planday', $sunday[2])->get();
        $data=array();
        foreach ($plans as $plan) {
            if (isset($plan->person->individual->user)) {
                $data[$plan->person_id]['societies'][$plan->service->society->society][]=$plan->service->servicetime;
                $data[$plan->person_id]['name'] = $plan->person->individual->title . ' ' . $plan->person->individual->firstname . ' ' . $plan->person->individual->surname;
                $data[$plan->person_id]['user_id'] = $plan->person->individual->user->id;
            }
        }
        foreach ($data as $dat) {
            if ($dat['user_id'] == 1) {
                $message = "This is a reminder that you are preaching this Sunday (" . $nextsunday . ") at:\n";
                foreach ($dat['societies'] as $key=>$soc) {
                    $message = $message . "\n" . $key . ": " . implode(', ', $soc);
                }
                $message = $message . "\n\nThe lectionary readings for Sunday are: " . implode(', ', $readings['readings']) . ".\n\nThank you so much for your willingness to be used by God as a preacher of the gospel!";
                $reminder = Reminder::create(['user_id'=>$dat['user_id'], 'message'=>$message]);
                Notification::send(User::find($dat['user_id']), new PushNotification($dat['name'], $message));
            }
        }
    }
}
