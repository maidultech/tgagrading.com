<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BalanceMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Credit added to your ' . (getSetting()->site_name ?? config('app.name')) . ' wallet!';
        $email = $this->subject($subject)
                ->from(
                    isset($this->data['from'][1]) ? $this->data['from'][1] : 'support@tgagrading.com' ,
                    isset($this->data['from'][0]) ? $this->data['from'][0] : "TGA Grading Support" 
                    )
                ->view('emails.balance-mail');

        return $email;
    }
}
