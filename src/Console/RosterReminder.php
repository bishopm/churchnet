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
        
        $items = Rosteritem::with('rostergroup.roster')->where('rosterdate',date('Y-m-d'))->first();
        
        $message = "This is a reminder that you are preaching this Sunday at:\n";
        $message = $message . "\n\nThe lectionary readings for Sunday are: ";
        // $reminder = Reminder::create(['user_id'=>1, 'message'=>$message]);
        // Notification::send(User::find(1), new PushNotification('Michael Bishop', $message));
    }
}
