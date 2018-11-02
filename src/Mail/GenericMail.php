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
        //if ($this->emaildata->hasFile('attachment')) {
        //    return $this->subject($this->emaildata->title)
        //            ->from($this->emaildata->sender)
        //            ->attach($this->emaildata->file('attachment'), array('as' => $this->emaildata->file('attachment')->getClientOriginalName(), 'mime' => $this->emaildata->file('attachment')->getMimeType()))
        //            ->markdown('churchnet::emails.generic');
        //} else {
        return $this->subject($this->emaildata['title'])
                    ->from($this->emaildata['sender'])
                    ->markdown('churchnet::emails.generic');
        //}
    }
}
