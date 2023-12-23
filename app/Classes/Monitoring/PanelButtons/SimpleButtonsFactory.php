<?php
namespace App\Classes\Monitoring\PanelButtons;

use App\MonitoringProject;
use App\User;

class SimpleButtonsFactory
{
    public function createButtons(User $user, MonitoringProject $project): array
    {
        return [
            (new ProjectButton($user, $project))->get(),
            (new CompetitorButton($user, $project))->get(),
            (new TopAnalysisButton($user, $project))->get(),
            (new PromotionPlanButtons($user, $project))->get(),
            (new SiteAuditButtons($user, $project))->get(),
            (new LinkTrackingButtons($user, $project))->get(),
        ];
    }
}
