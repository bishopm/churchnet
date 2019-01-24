<?php

namespace Bishopm\Churchnet\Mail;

use Illuminate\Mail\Mailable;

class GenericMail extends Mailable
{

    /**
     * Create a new message instance.
     *
     * @return void
     */
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
        if (array_key_exists('file',$this->emaildata)) {
            return $this->subject($this->emaildata['title'])
                    ->from($this->emaildata['sender'])
                    ->attach($this->emaildata['file'], ['as' => $this->emaildata['attachment']['name'], 'mime' => $this->emaildata['attachment']['type']])
                    ->markdown('churchnet::emails.generic');
        } else {
        return $this->subject($this->emaildata['title'])
                    ->from($this->emaildata['sender'])
                    ->markdown('churchnet::emails.generic');
        }
    }
}
