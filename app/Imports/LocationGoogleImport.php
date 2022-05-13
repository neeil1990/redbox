<?php

namespace App\Imports;

use App\Location;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class LocationGoogleImport implements ToModel, WithProgressBar
{
    use Importable;

    public function model(array $row)
    {
        if(!is_numeric($row[0]))
            return null;

        return Location::updateOrCreate(
            ['source' => 'google', 'lr' => $row[0]],
            ['name' => $row[2]]
        );
    }
}
