<?php


namespace App\Classes\Tariffs\Settings;


use App\Classes\Tariffs\Interfaces\Settings;

class OptimalSettings implements Settings
{
    protected $settings;

    public function get(): array
    {

        $this->settings = [
            'duplicates_str_length' => 5
        ];

        return $this->settings;
    }
}
