<?php


namespace App\Classes\Monitoring\PanelButtons;


use App\Classes\Monitoring\PanelButtons\Templates\ButtonTemplate;
use App\MonitoringProject;
use App\User;

abstract class Buttons
{
    protected $user;
    protected $project;

    public function __construct(User $user, MonitoringProject $project)
    {
        $this->user = $user;
        $this->project = $project;
    }

    public function get()
    {
        $button = $this->createButton();
        return $button->display();
    }

    abstract protected function createButton(): ButtonTemplate;
}