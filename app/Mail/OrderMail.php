<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
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
        $defaultSubject = 'You have a new message from ' . (getSetting()->site_name ?? config('app.name'));
        $subject = $this->data['subject'] ?? $defaultSubject;

        $email = $this->subject($subject)
                ->from(
                    isset($this->data['from'][1]) ? $this->data['from'][1] : 'support@tgagrading.com' ,
                    isset($this->data['from'][0]) ? $this->data['from'][0] : "TGA Grading Support" 
                    )
                ->view('emails.order-mail');

        // Check if an invoice file path exists and attach it
        if (!empty($this->data['invoice_link']) && file_exists($this->data['invoice_link'])) {
            $email->attach($this->data['invoice_link'], [
                'as' => 'Invoice.pdf', // Rename attachment if needed
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
