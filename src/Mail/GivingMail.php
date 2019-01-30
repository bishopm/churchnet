<?php

namespace Bishopm\Churchnet\Mail;

use Illuminate\Mail\Mailable;

class GivingMail extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
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
            ->markdown('churchnet::emails.givingreport');
    }
}
