<?php


namespace App\Classes\Monitoring\PanelButtons;


use App\ChecklistMonitoringRelation;
use App\Classes\Monitoring\PanelButtons\Templates\ButtonTemplate;
use App\Classes\Monitoring\PanelButtons\Templates\DefaultButtonTemplate;

class PromotionPlanButtons extends Buttons
{

    protected function createButton(): ButtonTemplate
    {

        $relation = ChecklistMonitoringRelation::where('monitoring_id', $this->project->id)
            ->with('checklist')
            ->first();

        $temp = new DefaultButtonTemplate();

        $temp->content = $this->wrapTag(__('Promotion plan'), 'p');

        if (isset($relation)) {
            $checklistID = $relation->checklist->id;

            $temp->small = $this->wrapTag(
                __('Show checklist'),
                'button',
                'href="/checklist-tasks/' . $checklistID . '" target="_blank" class="btn btn-sm btn-danger change-tag"');

            $temp->actions = $this->wrapTag(
                __('Change checklist'),
                'button',
                'class="btn btn-danger btn-sm set-monitoring-relation" data-toggle="modal" data-target="#setRelation"'
            );

        } else {
            $temp->actions = $this->wrapTag(
                __('Relation checklist'),
                'button',
                'class="btn btn-danger btn-sm set-monitoring-relation" data-toggle="modal" data-target="#setRelation"'
            );
        }

        $temp->icon = 'far fa-check-square';
        $temp->bg = 'bg-danger';

        return $temp;
    }
}
