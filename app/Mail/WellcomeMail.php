<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WellcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

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
        return $this->subject("Welcome to " . getSetting()->site_name ?? 'tgagrading.com')
            ->from(env('MAIL_FROM_ADDRESS'))
            ->markdown('emails.welcome-mail',['user' => $this->data])
            ->with('user', $this->data);
    }
}
