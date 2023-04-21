<?php


namespace App\Classes\Monitoring\PanelButtons;


use App\Classes\Monitoring\PanelButtons\Templates\ButtonTemplate;
use App\Classes\Monitoring\PanelButtons\Templates\DefaultButtonTemplate;

class CompetitorButton extends Buttons
{
    protected function createButton(): ButtonTemplate
    {
        $temp = new DefaultButtonTemplate();

        $temp->h3 = $this->getCount();
        $temp->p = __('My competitors');
        $temp->href = route('monitoring.competitors', $this->project->id);
        $temp->icon = 'fas fa-user-secret';
        $temp->bg = 'bg-success';

        return $temp;
    }

    private function getCount()
    {
        return $this->project->competitors->count();
    }
}
