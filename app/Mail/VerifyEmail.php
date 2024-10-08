<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    public $user;

    public $url;

    public $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $url, $code)
    {
        $this->user = $user;
        $this->code = $code;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.files.verify_email')->subject($this->user->lang === 'ru' ? 'Верификация почты' : 'Verify mail');
    }
}
