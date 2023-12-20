<?php


namespace App\Classes\Monitoring\PanelButtons;


use App\Classes\Monitoring\PanelButtons\Templates\ButtonTemplate;
use App\Classes\Monitoring\PanelButtons\Templates\DefaultButtonTemplate;

class ProjectButton extends Buttons
{
    protected function createButton(): ButtonTemplate
    {
        $temp = new DefaultButtonTemplate();

        $temp->h3 = $this->getCount();
        $temp->content = $this->wrapTag(__('Projects'), 'p');
        $temp->href = route('monitoring.index');

        return $temp;
    }

    private function getCount()
    {
       return $this->user->monitoringProjects()->count();
    }
}
