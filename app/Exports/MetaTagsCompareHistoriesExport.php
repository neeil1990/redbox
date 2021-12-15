<?php

namespace App\Exports;

use App\MetaTagsHistory;
use Maatwebsite\Excel\Concerns\FromCollection;

class MetaTagsCompareHistoriesExport implements FromCollection
{
    protected $id;
    protected $id_compare;

    public function __construct($id, $id_compare)
    {
        $this->id = $id;
        $this->id_compare = $id_compare;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $history = MetaTagsHistory::findOrFail($this->id);
        $history_compare = MetaTagsHistory::findOrFail($this->id_compare);

        $response = [];
        $this->createCompareArray($history, 'card', $response);
        $this->createCompareArray($history_compare, 'card_compare', $response);

        foreach ($response as $url => &$item){

            $tags = [];
            foreach ($item['card']['tags'] as $tag => $val){
                $tags['url'] = $url;
                $tags[$tag] = is_array($val) ? implode(', ', $val) : "Нет проблем";;
                $tags[$tag . '_compare'] = is_array($item['card_compare']['tags']->$tag) ? implode(', ', $item['card_compare']['tags']->$tag) : "Нет проблем";
            }

            $item = $tags;
        }

        $csv = collect($response);

        if($csv->first()){

            $csv_title = [];
            foreach (array_keys($csv->first()) as $index => $title){
                if(!$index){
                    $csv_title[] = strtoupper($title);
                }else{
                    if(count(explode('_',$title)) === 1)
                        $csv_title[] = strtoupper($title) . ' ' . $history->created_at->format('d.m.Y') . '(' . $history->id . ')';
                    else
                        $csv_title[] = strtoupper($title) . ' ' . $history_compare->created_at->format('d.m.Y') . '(' . $history_compare->id . ')';

                }
            }

            $csv->prepend($csv_title);
        }
        
        return $csv;
    }

    protected function createCompareArray($model, $name = 'card', &$response = [])
    {
        $histories = json_decode($model->data);
        foreach ($histories as $item){
            $response[$item->title][$name]['date'] = $model->created_at->format('d.m.Y');
            $response[$item->title][$name]['tags'] = $item->data;
        }

        return $response;
    }
}
