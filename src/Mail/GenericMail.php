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
    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (array_key_exists('data',$this->emaildata['attachment'])) {
            return $this->subject($this->emaildata['title'])
                    ->from($this->emaildata['sender'])
                    ->replyTo($this->emaildata['sender'])
                    ->attachData(base64_decode(str_replace("data:application/pdf;base64,","",$this->emaildata['attachment']['data'])), $this->emaildata['attachment']['name'], ['mime' => $this->emaildata['attachment']['type']])
                    ->markdown('churchnet::emails.generic');
        } else {
        return $this->subject($this->emaildata['title'])
                    ->from($this->emaildata['sender'])
                    ->replyTo($this->emaildata['sender'])
                    ->markdown('churchnet::emails.generic');
        }
    }
}
