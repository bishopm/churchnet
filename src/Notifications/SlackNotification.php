<?php

namespace Bishopm\Churchnet\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class SlackNotification extends Notification
{
    use Queueable;

    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['slack'];
    }
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->from('ChurchNet')
            ->to('#journey-app')
            ->content($this->message);
    }
}
