<?php


namespace App\Classes\Monitoring\PanelButtons;


use App\Classes\Monitoring\PanelButtons\Templates\ButtonTemplate;
use App\Classes\Monitoring\PanelButtons\Templates\DefaultButtonTemplate;

class LinkTrackingButtons extends Buttons
{
    protected function createButton(): ButtonTemplate
    {
        $temp = new DefaultButtonTemplate();

        $temp->h3 = $this->user->backlingProjects()->count();
        $temp->content = $this->content();
        $temp->icon = 'fas fa-link';
        $temp->bg = 'bg-purple-light';
        $temp->href = route('backlink');
        $temp->small = __('Link tracking');

        return $temp;
    }

    private function content(): string
    {
        $data = [];
        return view('monitoring.buttons-panel.link_tracking_content', compact('data'));
    }
}
