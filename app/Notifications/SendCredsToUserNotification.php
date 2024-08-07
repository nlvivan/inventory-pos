<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendCredsToUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $email = [
            'subject' => 'New Account',
            'body' => "Hi {$notifiable->name},

            This is to inform you that this is your account.

            <b>Name:</b> {$notifiable->name}
            <b>Email:</b> {$notifiable->email}
            <b>Password:</b> password

            Thank you
            ",
        ];

        $mailMessage = (new MailMessage)
            ->subject($email['subject']);

        return $mailMessage->markdown('emails.welcome-email', [
            'body' => $email['body'],
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
