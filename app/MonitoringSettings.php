<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringSettings extends Model
{
    protected $fillable = ['name', 'value'];

    public function scopeGetValuesAsArray($query, $fields = [])
    {
        $result = $query->whereIn('name', $fields)->get()->pluck('value', 'name');

        foreach ($fields as $field)
            if(!$result->has($field))
                $result->put($field, null);

        return $result;
    }

    public function scopeGetValue($query, $field = null)
    {
        if($result = $query->whereNotNull('value')->where('name', $field)->first('value'))
            return $result['value'];

        return false;
    }
}
