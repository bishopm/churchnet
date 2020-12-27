<?php

namespace Bishopm\Churchnet\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class GivingMail extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data=$data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->data['title'])
            ->from($this->data['sender'])
            ->replyTo($this->data['sender'])
            ->markdown('churchnet::emails.givingreport');
    }
}
