<?php


namespace App\Classes\Position;


use App\Classes\Xml\XmlFacade;
use App\Exceptions\ErrorXmlPositionResponseException;
use App\SearchIndex;
use Carbon\Carbon;

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
        $this->xml->setGroupBy('attr=d.mode=deep.groups-on-page=100.docs-in-group=1');
        $this->xml->setLr($this->lr);
        $this->xml->setQuery($this->query);
    }

    /**
     * @return array|int|string|null
     * @throws ErrorXmlPositionResponseException
     */
    protected function getSitePosition()
    {
        $site = $this->domain;
        $results = $this->xml->getByArray();

        if (!isset($results['response']['error'])) {

            $positions = $results['response']['results']['grouping']['group'];

            if ($this->save)
                $this->store($positions);

            $position = array_filter($positions, function ($var) use ($site) {
                $domain = parse_url($var['doc']['url']);
                return $this->domainFilter($domain['host']) === $site;
            });

            if (count($position) > 0) {
                $posKey = key($position);
                $position[$posKey]["doc"]["position"] = ($posKey + 1);

                return $position[$posKey]["doc"];
            } else
                return null;

        } else
            throw new ErrorXmlPositionResponseException($results['response']['error']);
    }

    private function store($positions)
    {
        if (!count($positions))
            return null;

        $create = [];

        $lr = $this->lr;
        $query = $this->query;
        $source = get_class($this);

        foreach ($positions as $index => $position) {

            $url = $position['doc']['url'] ?? null;
            $title = $position['doc']['title'] ?? null;
            $passages = $position['doc']['passages'] ?? null;
            if (isset($passages['passage']))
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
