<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class MetaTagsEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $file;
    protected $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $file)
    {
        $this->file = $file;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->name)->markdown('emails.files.meta_tags', [
            'message' => 'Проверка выполнена, результат проверки вы можете скачать в приложении к письму.'
        ])->attach($this->file, [
            'as' => Str::snake($this->name, '_') . '.pdf',
            'mime' => 'application/pdf',
        ]);
    }
}
