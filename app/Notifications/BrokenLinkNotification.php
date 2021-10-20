<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BrokenLinkNotification extends Notification
{
    use Queueable;

    private $request;
    private $link;

    /**
     * Create a new notification instance.
     *
     * @param $request
     */
    public function __construct($request, $link)
    {
        $this->request = $request;
        $this->link = $link;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->line('Site donor: ' . $this->link->site_donor)
            ->line('Link' . $this->link->link)
            ->line('Anchor' . $this->link->anchor)
            ->line('error: ' . $this->request)
            ->action('Check your projects', route('backlink'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
