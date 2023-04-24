<?php


namespace App\Classes\Monitoring\PanelButtons;


use App\Classes\Monitoring\PanelButtons\Templates\ButtonTemplate;
use App\Classes\Monitoring\PanelButtons\Templates\DefaultButtonTemplate;

class LinkTrackingButtons extends Buttons
{
    protected function createButton(): ButtonTemplate
    {
        $temp = new DefaultButtonTemplate();

        $temp->content = $this->content();
        $temp->icon = 'fas fa-link';
        $temp->bg = 'bg-purple-light';
        $temp->href = route('backlink');
        $temp->small = __('Link tracking');

        return $temp;
    }

    private function content(): string
    {
        $backLinks = $this->backLinks();
        return view('monitoring.buttons-panel.link_tracking_content', compact('backLinks'));
    }

    private function backLinks(): array
    {
        $total = 0;
        $broken = 0;

        $backLinks = $this->project->backlinks;
        foreach ($backLinks as $item){
            $total += $item['total_link'];
            $broken += $item['total_broken_link'];
        }

        $work = $total - $broken;

        return compact('total', 'work', 'broken');
    }
}
