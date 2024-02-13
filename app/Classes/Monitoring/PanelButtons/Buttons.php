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

    protected function wrapTag(string $str, string $tag, $attributes = null): string
    {
        if (isset($attributes)) {
            return "<$tag $attributes>$str</$tag>";
        } else {
            return '<' . $tag . '>' . $str . '</' . $tag . '>';
        }
    }

    abstract protected function createButton(): ButtonTemplate;
}
