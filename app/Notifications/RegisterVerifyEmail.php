<?php

namespace App\Notifications;

use App\Mail\VerifyEmail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Spatie\Url\Url as SpatieUrl;

class RegisterVerifyEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        /**
         * @var $user User
         */
        $user = User::latest()->first();
        $verificationUrl = $this->verificationUrl($notifiable);
        $verificationCode = $this->verificationCode($verificationUrl);

//        Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl, $verificationCode));

        if ($user->lang === 'ru') {
            return (new MailMessage)
                ->greeting('Привет, ' . $user->name . '.')
                ->subject(Lang::getFromJson('Подтверждение регистрации'))
                ->line(Lang::getFromJson('Пожалуйста, нажмите на кнопку ниже, чтобы подтвердить свой адрес электронной почты.'))
                ->line('Ваш верификационный код: ' . $verificationCode)
                ->action(Lang::getFromJson('Нажмите сюда'), $verificationUrl)
                ->line(Lang::getFromJson('Если вы не создавали учетную запись, никаких дальнейших действий не требуется.'));
        } else {
            return (new MailMessage)
                ->greeting('Hello, ' . $user->name . '.')
                ->subject(Lang::getFromJson('Verify Email Address'))
                ->line(Lang::getFromJson('Please click the button below to verify your email address.'))
                ->line('Verify Input Code: ' . $verificationCode)
                ->action(Lang::getFromJson('Verify Email Address'), $verificationUrl)
                ->line(Lang::getFromJson('If you did not create an account, no further action is required.'));
        }
    }

    public function sendMessage($notifiable)
    {
        $user = User::latest()->first();
        $verificationUrl = $this->verificationUrl($notifiable);
        $verificationCode = $this->verificationCode($verificationUrl);

        Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl, $verificationCode));
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey()]
        );
    }

    /**
     * Get the verification POST Code for the given notifiable.
     *
     */
    private function verificationCode($code)
    {
        $code = SpatieUrl::fromString($code);
        $code = $code->getQueryParameter('expires');
        session(['verificationCode' => $code]);

        return $code;
    }
}
