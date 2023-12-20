<?php

namespace App\Mail;

use App\MonitoringProject;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MonitoringShareProjectMail extends Mailable
{
    use Queueable, SerializesModels;

    private $project;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MonitoringProject $project)
    {
        $this->project = $project;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $project = $this->project;
        return $this->subject(__('You have been given access to the project') . " " . $project['name'])
            ->markdown('emails.monitoring.share', compact('project'));
    }
}
