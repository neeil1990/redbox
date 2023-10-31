<?php


namespace App\Classes\Xml;

use Ixudra\Curl\Facades\Curl;

class XmlFacade
{
    /**
     * Урл запроса
     *
     * @var string
     */
    protected $path = 'https://xmlstock.com/yandex/xml/';

    /**
     * имя пользователя
     *
     * @var string
     */
    protected $user = '9371';

    /**
     * API-ключ
     *
     * @var string
     */
    protected $key = '660fb3c4c831f41ac36637cf3b69031e';

    /**
     * текст поискового запроса
     *
     * @var string
     */
    protected $query = '';

    /**
     * идентификатор страны/региона поиска
     *
     * @var string
     */
    protected $lr = '193';

    /**
     * тип сортировки
     *
     * @var string
     */
    protected $sortby = 'rlv';

    /**
     * метод группировки
     *
     * @var string
     */
    protected $groupby = 'deep';

    /**
     * номер страницы
     *
     * @var string
     */
    protected $page = '0';

    /**
     * @param string $path
     * @return XmlFacade
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $user
     * @return XmlFacade
     */
    public function setUser(string $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string $key
     * @return XmlFacade
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param string $query
     * @return XmlFacade
     */
    public function setQuery(string $query)
    {
        $this->query = $query;
        $this->setAttempt();

        return $this;
    }

    /**
     * @param string $lr
     * @return XmlFacade
     */
    public function setLr(string $lr)
    {
        $this->lr = $lr;

        return $this;
    }

    /**
     * @param string $sortby
     * @return XmlFacade
     */
    public function setSortby(string $sortby)
    {
        $this->sortby = $sortby;

        return $this;
    }

    /**
     * @param string $groupby
     * @return XmlFacade
     */
    public function setGroupBy(string $groupby)
    {
        $this->groupby = $groupby;

        return $this;
    }

    /**
     * @param string $page
     * @return XmlFacade
     */
    public function setPage(string $page)
    {
        $this->page = $page;

        return $this;
    }

    private function filter($xml)
    {
        return str_replace(['<hlword>', '</hlword>'], '', $xml);
    }

    /**
     * Get as array
     */
    public function getByArray()
    {
        $response = Curl::to($this->path)
            ->withData( $this->buildQuery() )
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $content = $this->filter($response->content);

        if($response->status == 404)
            throw new \InvalidArgumentException('Wrong path or request, Check field path!');

        $xml = $this->load($content);
        $json = json_encode($xml);

        return json_decode($json,TRUE);
    }

    /**
     * get as object
     * @return \SimpleXMLElement
     */
    public function getByObject()
    {
        $response = Curl::to($this->path)
            ->withData( $this->buildQuery() )
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $content = $response->content;

        if($response->status == 404)
            throw new \InvalidArgumentException('Wrong path or request, Check field path!');

        $xml = $this->load($content);

        return $xml;
    }

    /**
     * Return URL request.
     *
     * @return string|null
     */
    public function getQueryURL()
    {
        if($this->path && $this->buildQuery())
            return $this->path . '?' . http_build_query($this->buildQuery());
        else
            return null;
    }

    /**
     * @param $xml
     * @return \SimpleXMLElement
     */
    protected function load($xml)
    {
        return simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
    }

    /**
     * @return array
     */
    protected function buildQuery()
    {
        return  [
            'user' => $this->user,
            'key' => $this->key,
            'query' => $this->query,
            'lr' => $this->lr,
            'sortby' => $this->sortby,
            'page' => $this->page,
            'groupby' => $this->groupby
        ];
    }

}
