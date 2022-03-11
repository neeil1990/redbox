<?php


namespace App\Classes\Tariffs\Interfaces;


interface Period
{
    public function name(): string;

    public function code(): string;

    public function setPrice(int $price);

    public function setDays(int $days);

    public function setMonths(int $months);

    public function percent(): int;

    public function price(): int;

    public function discount(): int;

    public function total(): int;

    public function days(): int;
}
