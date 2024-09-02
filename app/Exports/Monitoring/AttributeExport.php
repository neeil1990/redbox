<?php


namespace App\Exports\Monitoring;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class AttributeExport
{
    private $collection;
    private $request;
    private $budget = 0;

    public function __construct(Collection &$collection, Request $request)
    {
        $this->collection = $collection;
        $this->request = $request;
    }

    public function execute()
    {
        if ($this->request['mode'] == 'finance') {
            $this->setTotalSum($this->getBudget());
        }

        if ($this->request['dynamicsDays']) {
            $this->removeDynamicDays();
        }

        $this->url();
    }

    protected function removeDynamicDays()
    {
        $this->collection['data']->transform(function($item) {
            foreach ($item as $col => $val) {
                $item[$col] = preg_replace('/<sup(.*)sup>/', '', $val);
            }

            return $item;
        });
    }

    protected function setTotalSum($budget)
    {
        $total = $this->collection['data']->pluck('mastered')->sum();
        $count = $this->collection['columns']->count();

        $this->collection['data']->push(collect(['Выведено фраз на сумму:', $total])->pad(-$count, ''));
        $this->collection['data']->push(collect(['Максимальный бюджет:', $budget])->pad(-$count, ''));
    }

    protected function url()
    {
        $this->collection['data']->transform(function($item){
            if ($item->has('url')) {
                $url = $item['url'];

                $doc = new \DOMDocument();
                $doc->loadHTML($url);

                $a = $doc->getElementsByTagName('a');
                $links = $a[0]->getAttribute('data-content');

                if ($links) {
                    $doc->loadHTML($links);
                    $a = $doc->getElementsByTagName('a');

                    if ($a->length) {
                        $item['url'] = strip_tags($a[$a->length - 1]->textContent);
                    }
                }
            }
            return $item;
        });
    }

    public function getBudget(): int
    {
        return $this->budget;
    }

    public function setBudget($budget): void
    {
        $this->budget = $budget;
    }


}
