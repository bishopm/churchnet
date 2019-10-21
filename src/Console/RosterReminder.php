<?php

namespace Bishopm\Churchnet\Console;

use Illuminate\Console\Command;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Rosteritem;
use Bishopm\Churchnet\Models\Reminder;
use Bishopm\Churchnet\Notifications\PushNotification;
use Illuminate\Support\Facades\Notification;

class RosterReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'churchnet:rosterreminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a weekly roster reminder to members';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $remindertime = strtotime(date('Y-m-d')) + (86400 * 6);
        $items = Rosteritem::with('rostergroup.roster','rostergroup.group','individuals.user')->where('rosterdate',date('Y-m-d',$remindertime))->get();
        foreach ($items as $item) {
            if (isset($item->individuals)) {
                foreach ($item->individuals as $indiv) {
                    if (isset($indiv->user->id)) {
                        $message = $indiv->firstname . " " . $indiv->surname . ": A roster reminder for " . date('D d M',strtotime($item->rosterdate)) . " (" . $item->rostergroup->group->groupname . ")\n";
                        Reminder::create(['user_id'=>$indiv->user->id, 'message'=>$message]);
                        Notification::send(User::find($indiv->user->id), new PushNotification($indiv->firstname . ' ' . $indiv->surname, $message));
                    }
                }
            } 
        }
    }
}
