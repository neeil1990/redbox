<?php


namespace App\Classes\Position;


use App\Classes\Xml\XmlFacade;
use Illuminate\Support\Facades\Log;

abstract class Positions
{
    protected $engine;
    protected $xml;

    public $domain;
    public $query;
    public $current_page = 0;
    public $max_page = 50;
    public $lr;

    public function __construct()
    {
        $this->xml = new XmlFacade();
    }

    public function handle()
    {
        $this->setParams();
        $position = $this->getSitePosition(0, $this->max_page);

        dd($position, $this->xml->getQueryURL());
    }

    protected function setParams()
    {
        $this->xml->setPath($this->engine);
        $this->xml->setGroupBy('flat');
        $this->xml->setLr($this->lr);
        $this->xml->setQuery($this->query);
    }

    protected function getSitePosition($page = 0, $max = 10)
    {
        if($this->current_page > $max)
            return null;

        $site = $this->domain;
        $this->xml->setPage($page);
        $results = $this->xml->getByArray();

        if(!isset($results['response']['error'])){
            $this->current_page = $results['response']['results']['grouping']['page'];
            $positions = $results['response']['results']['grouping']['group'];

            $position = array_filter($positions, function($var) use ($site) {
                $domain = parse_url($var['doc']['url']);
                return str_replace(['www.'], '', strtolower($domain['host'])) === $site;
            });

            if(count($position) > 0){
                $p = $page;
                $n = count($positions);
                $count = $n * $p;

                $position = key($position) + 1;

                $sum = $count + $position;

                return $sum;
            }else{
                $page = $page + 1;
                return $this->getSitePosition($page);
            }
        }else{
            Log::error($results);
        }
    }
}
