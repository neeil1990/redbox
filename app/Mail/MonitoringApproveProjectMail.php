<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MonitoringApproveProjectMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $project;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $project)
    {
        $this->user = $user;
        $this->project = $project;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->user;
        $project = $this->project;

        return $this->markdown('emails.monitoring.approve', compact('user', 'project'));
    }
}
