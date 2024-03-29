<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class sendNotificationAboutExpirationRegistrationPeriod extends Notification
{
    use Queueable;

    public $project;

    public $diffInDays;

    /**
     * Create a new notification instance.
     *
     * @param $project
     * @param $diffInDays
     */
    public function __construct($project, $diffInDays)
    {
        $this->project = $project;
        $this->diffInDays = $diffInDays;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
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
        if($notifiable->lang === 'ru'){
            return (new MailMessage)
                ->line('Это сообщение сгенерированно автоматически, на него не нужно отвечать.')
                ->line('Домен ' . $this->project->domain)
                ->line("Регистрация заканчивается через $this->diffInDays дней")
                ->action('Проверьте ваши проекты', route('domain.information'))
                ->line('Спасибо за то что вы с нами!');
        } else {
            return (new MailMessage)
                ->line('This message is generated automatically and does not need to be answered.')
                ->line('Domain ' . $this->project->domain)
                ->line("Registration ends after $this->diffInDays days")
                ->action('Check your projects', route('domain.information'))
                ->line('Thank you for using our application!');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
