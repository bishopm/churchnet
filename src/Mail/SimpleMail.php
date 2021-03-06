<?php

namespace Bishopm\Churchnet\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class SimpleMail extends Mailable
{

    /**
     * Create a new message instance.
     *
     * @return void
     */
    use Queueable, SerializesModels;

    public $emaildata;

    public function __construct($emaildata)
    {
        $this->emaildata=$emaildata;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emaildata['title'])
        ->from($this->emaildata['sender'])
        ->replyTo($this->emaildata['sender'])
        ->markdown('churchnet::emails.simple');
    }
}
