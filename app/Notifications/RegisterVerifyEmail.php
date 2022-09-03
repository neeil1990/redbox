<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
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
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        /**
         * @var $user User
         */
        $user = User::latest()->first();
        Log::debug('register user_id', [
            $user,
        ]);
        $verificationUrl = $this->verificationUrl($notifiable);
        $verificationCode = $this->verificationCode($verificationUrl);

        return (new MailMessage)
            ->greeting('Hello, dear')
            ->subject(Lang::getFromJson('Verify Email Address'))
            ->line(Lang::getFromJson('Please click the button below to verify your email address.'))
            ->line('Verify Input Code: ' . $verificationCode)
            ->action(Lang::getFromJson('Verify Email Address'), $verificationUrl)
            ->line(Lang::getFromJson('If you did not create an account, no further action is required.'));
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
