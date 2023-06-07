<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RegisterPasswordEmail extends Notification
{
    use Queueable;

    private $request;

    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($request, $user)
    {
        $this->request = $request;
        $this->user = $user;
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $password = $this->request->input('password', null);

        if ($this->user->lang === 'ru') {
            return (new MailMessage)
                ->subject(__('Reset password'))
                ->line('Send the password reset notification.')
                ->line('Your new password: ' . $password)
                ->action('Your profile', url('/profile'))
                ->line('Thank you for using our application!');
        } else {
            return (new MailMessage)
                ->subject(__('Сброс пароля'))
                ->line('Уведомление о сбросе пароля.')
                ->line('Ваш новый пароль: ' . $password)
                ->action('Ваш профиль', url('/profile'))
                ->line('Благодарим вас за использование нашего приложения!');
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
