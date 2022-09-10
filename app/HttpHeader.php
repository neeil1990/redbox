<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class HttpHeader extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['data'];

    /**
     * @var int
     */
    protected $deleteDays = 60;

    /**
     * @param array $data
     * @return |id
     */
    public function saveData(array $data = []){
        if(!$data)
            return null;

        $collection = collect($data);
        $data = $collection->map(function($item, $key){
            $item['content'] = base64_encode($item['content']);
            return $item;
        });

        $httpHeader = $this->create(['data' => serialize($data->toArray())]);

        return $httpHeader->id;
    }

    /**
     * @param int|null $id
     * @return array
     */
    public function getData(int $id = null)
    {
        $header = $this->findOrFail($id);
        $collection = collect(unserialize($header->data));
        $data = $collection->map(function($item, $key){
            $item['content'] = base64_decode($item['content']);
            return $item;
        });

        return $data->toArray();
    }

    /**
     * Delete database row HttpHeaders where date create more $deleteDays.
     */
    public function deleteData()
    {
        $this->where('created_at', '<=', Carbon::now()->subDays($this->deleteDays))->delete();
    }
}
