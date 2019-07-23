<?php

namespace Bishopm\Churchnet\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emaildata;

    public function __construct($emaildata)
    {
        $this->emaildata=$emaildata;
        return $emaildata;
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
                    ->replyTo($this->emaildata['sender'])
                    ->attach($this->emaildata['file'], ['as' => $this->emaildata['attachment']['name'], 'mime' => $this->emaildata['attachment']['type']])
                    ->markdown('churchnet::emails.generic');
        } else {
        return $this->subject($this->emaildata['title'])
                    ->from($this->emaildata['sender'])
                    ->replyTo($this->emaildata['sender'])
                    ->markdown('churchnet::emails.generic');
        }
    }
}
