<?php


namespace App\Classes\Monitoring\PanelButtons\Templates;


class DefaultButtonTemplate implements ButtonTemplate
{
    public $h3 = '';
    public $content = 'Untitled';
    public $icon = 'fas fa-home';
    public $href = '#';
    public $bg = 'bg-info';
    public $small = '';

    public function display(): array
    {
        return [
            'h3' => $this->h3,
            'content' => $this->content,
            'icon' => $this->icon,
            'href' => $this->href,
            'bg' => $this->bg,
            'small' => $this->small,
        ];
    }
}
