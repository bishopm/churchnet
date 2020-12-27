<?php

namespace Bishopm\Churchnet\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class MonthlyBookshopMail extends Mailable
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
        return $this->subject($this->data['subject'])
            ->from($this->data['sender'])
            ->replyTo($this->data['sender'])
            ->markdown('connexion::emails.monthlybookshop');
    }
}
