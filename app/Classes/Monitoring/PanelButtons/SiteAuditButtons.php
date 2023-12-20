<?php


namespace App\Classes\Monitoring\PanelButtons;


use App\Classes\Monitoring\PanelButtons\Templates\ButtonTemplate;
use App\Classes\Monitoring\PanelButtons\Templates\DefaultButtonTemplate;

class SiteAuditButtons extends Buttons
{

    protected function createButton(): ButtonTemplate
    {
        $temp = new DefaultButtonTemplate();

        $temp->content = $this->wrapTag(__('Site audit'), 'p');
        $temp->icon = 'fas fa-tasks';
        $temp->bg = 'bg-info';
        $temp->small = __('In developing');

        return $temp;
    }
}
