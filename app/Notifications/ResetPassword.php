<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword as Reset;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Reset
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(config('app.client_url') . '/password/reset/' . $this->token) . '?email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset request for your account .')
            ->action('Reset Password', $url)
            ->line('If you didnt request a password reset, no further action is requerd');
    }
}
