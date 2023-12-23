<?php


namespace App\Classes\Monitoring\PanelButtons;


use App\Classes\Monitoring\PanelButtons\Templates\ButtonTemplate;
use App\Classes\Monitoring\PanelButtons\Templates\DefaultButtonTemplate;

class TopAnalysisButton extends Buttons
{

    protected function createButton(): ButtonTemplate
    {
        $temp = new DefaultButtonTemplate();

        $temp->content = $this->wrapTag(__('TOP-100 analysis'), 'p');
        $temp->href = route('monitoring.top100', $this->project->id);
        $temp->icon = 'fas fa-chart-pie';
        $temp->bg = 'bg-warning';

        return $temp;
    }
}
