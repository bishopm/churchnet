<?php

namespace Bishopm\Churchnet\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Bishopm\Churchnet\Models\Setting;

class MonthlySupplierMail extends Mailable
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
        $data['sender']=Setting::where('setting_key','church_email')->first()->setting_value;
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
            ->markdown('connexion::emails.monthlysupplier');
    }
}
