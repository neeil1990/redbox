<?php


namespace App\Classes\Tariffs\Settings;


use App\Classes\Tariffs\Interfaces\Settings;

class FreeSettings implements Settings
{
    protected $settings;

    public function get(): array
    {
        $this->settings = [
            'duplicates_str_length' => 2
        ];

        return $this->settings;
    }
}
