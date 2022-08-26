<?php


namespace App\Classes\Position;


use App\Classes\Xml\XmlFacade;
use App\SearchIndex;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

abstract class Positions
{
    protected $engine;
    protected $xml;
    protected $save = true;

    public $domain;
    public $query;
    public $lr;

    public function __construct()
    {
        $this->xml = new XmlFacade();
    }

    public function handle()
    {
        $this->setParams();

        return $this->getSitePosition();
    }

    protected function setParams()
    {
        $this->xml->setPath($this->engine);
        $this->xml->setGroupBy('flat.groups-on-page=100');
        $this->xml->setLr($this->lr);
        $this->xml->setQuery($this->query);
    }

    /**
     * @return array|int|string|null
     */
    protected function getSitePosition()
    {
        $site = $this->domain;
        $results = $this->xml->getByArray();

        if(!isset($results['response']['error'])){

            $positions = $results['response']['results']['grouping']['group'];

            if($this->save)
                $this->store($positions);

            $position = array_filter($positions, function($var) use ($site) {
                $domain = parse_url($var['doc']['url']);
                return $this->domainFilter($domain['host']) === $site;
            });

            if(count($position) > 0){
                $posKey = key($position);
                $position[$posKey]["doc"]["position"] = ($posKey + 1);

                return $position[$posKey]["doc"];
            }else
                return null;

        }else{
            $errors = [
                'search' => $this->engine,
                'region' => $this->lr,
                'error' => $results['response']['error'],
                'result' => $results,
            ];

            Log::error($errors);
        }
    }

    private function store($positions)
    {
        if(!count($positions))
            return null;

        $create = [];

        $lr = $this->lr;
        $query = $this->query;
        $source = get_class($this);

        foreach ($positions as $index => $position){

            $url = isset($position['doc']['url']) ? $position['doc']['url'] : null;
            $title = isset($position['doc']['title']) ? $position['doc']['title'] : null;
            $passages = isset($position['doc']['passages']) ? $position['doc']['passages'] : null;
            if(isset($passages['passage']))
                $snippet = (is_array($passages['passage'])) ? implode(', ', $passages['passage']) : $passages['passage'];
            else
                $snippet = null;

            $index = $index + 1;

            $create[$index] = [
                'source' => $source,
                'lr' => $lr,
                'url' => $url,
                'position' => $index,
                'title' => $title,
                'snippet' => $snippet,
                'query' => $query,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        SearchIndex::insert($create);
    }

    private function domainFilter($domain)
    {
        return str_replace(['www.'], '', strtolower($domain));
    }
}
