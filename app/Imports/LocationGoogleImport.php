<?php

namespace App\Imports;

use App\Location;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class LocationGoogleImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        if(!is_numeric($row[0]))
            return null;

        Location::updateOrCreate([
            'source' => 'google',
            'lr' => $row[0],
            'name' => $row[2],
        ]);
    }
}
