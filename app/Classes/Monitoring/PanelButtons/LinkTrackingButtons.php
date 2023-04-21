<?php


namespace App\Classes\Monitoring\PanelButtons;


use App\Classes\Monitoring\PanelButtons\Templates\ButtonTemplate;
use App\Classes\Monitoring\PanelButtons\Templates\DefaultButtonTemplate;

class LinkTrackingButtons extends Buttons
{
    protected function createButton(): ButtonTemplate
    {
        $temp = new DefaultButtonTemplate();

        $temp->h3 = $this->getCount();
        $temp->p = __('Link tracking');
        $temp->icon = 'fas fa-link';
        $temp->bg = 'bg-purple-light';
        $temp->href = route('backlink');

        return $temp;
    }

    private function getCount()
    {
        return $this->user->backlingProjects()->count();
    }
}
