<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Merci pour votre message !')
            ->greeting('Bonjour ' . $this->name)
            ->line('Merci de nous avoir contactés. Nous reviendrons vers vous très vite.')
            ->line('Ceci est un email automatique.');
    }
}
