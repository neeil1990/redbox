<?php


namespace App\Classes\Monitoring\PanelButtons;


use App\Classes\Monitoring\PanelButtons\Templates\ButtonTemplate;
use App\Classes\Monitoring\PanelButtons\Templates\DefaultButtonTemplate;

class PromotionPlanButtons extends Buttons
{

    protected function createButton(): ButtonTemplate
    {
        $temp = new DefaultButtonTemplate();

        $temp->p = __('Promotion plan');
        $temp->small = __('In developing');
        $temp->icon = 'far fa-check-square';
        $temp->bg = 'bg-danger';

        return $temp;
    }
}
