<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BrokenDomainNotification extends Notification
{
    use Queueable;

    public $project;

    /**
     * Create a new notification instance.
     *
     * @param $project
     */
    public function __construct($project)
    {
        $this->project = $project;
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
        if ($notifiable->lang === 'ru') {
            return (new MailMessage)
                ->line('Это сообщение было сгенерированно автоматически, на него не нужно отвечать')
                ->line('сайт ' . $this->project->link . ' отправил не корректный ответ')
                ->line('Статус код:' . $this->project->status)
                ->line('Текущий аптайм: ' . $this->project->uptime_percent . '%')
                ->action('Проверьте свои проекты', route('site.monitoring'))
                ->line('Спасибо, что пользуетесь нашим сервисом!');
        } else {
            return (new MailMessage)
                ->line('This message is generated automatically and does not need to be answered.')
                ->line('Domain ' . $this->project->link . ' broken')
                ->line('Status code: ' . $this->project->status)
                ->line('Uptime: ' . $this->project->uptime_percent . '%')
                ->action('Check your projects', route('site.monitoring'))
                ->line('Thank you for using our application!');
        }

    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
