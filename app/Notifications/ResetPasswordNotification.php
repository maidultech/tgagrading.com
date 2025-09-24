<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public $token;
    protected $siteName;

    public function __construct($token, $siteName)
    {
        $this->token = $token;
        $this->siteName = $siteName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
        ->from('support@tgagrading.com', 'TGA Grading Support')
        ->subject('Reset Password Notification')
        ->view('emails.reset_password', [
            'data' => [
                'greeting' => 'Hello!',
                'body' => 'You are receiving this email because we received a password reset request for your account.',
                'expiry' => 'This password reset link will expire in 60 minutes.',
                'verificationUrl' => $url,
                'note' => 'If you did not request a password reset, no further action is required.',
                'site_name' => $this->siteName,
                'site_url'  => route('frontend.index'),
            ]
        ]);
    }
}
