<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MonitoringPosition;
use Faker\Generator as Faker;

$factory->define(MonitoringPosition::class, function (Faker $faker) {
    return [
        'monitoring_keyword_id' => 10,
        'monitoring_searchengine_id' => 5,
        'position' => rand(1, 100),
        'target' => 10,
    ];
});
