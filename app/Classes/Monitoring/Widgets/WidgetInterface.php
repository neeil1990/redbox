<?php


namespace App\Classes\Monitoring\Widgets;


use Illuminate\Support\Collection;

interface WidgetInterface
{
    public function activation(bool $status): void;

    public function widget(): Collection;

    public function generateTitle(): string;

    public function generateDesc(): string;

    public function getCode(): string;

    public function getName(): string;
}
