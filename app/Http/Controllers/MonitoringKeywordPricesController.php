<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringKeywordPricesController extends Controller
{
    protected $user;
    protected $project;
    protected $request;
    protected $regions;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = $this->user;
        $this->project = $user->monitoringProjects()->find($request['id']);
        if(!$this->project)
            abort('404');

        $this->regions = $this->project->searchengines()->with('location')->get();
        $this->request = $request;

        if($request->ajax())
            return $this->getDataTable();

        return view('monitoring.price.index');
    }

    public function getKeywordsWithPrice()
    {
        $request = $this->request;
        $region = $request->input('region', $this->regions->first()['id']);

        $model = $this->project->keywords()->with(['prices' => function($query) use ($region){
            $query->where('monitoring_searchengine_id', $region);
        }]);

        if($search = $request->input('search')['value'])
            $model->where('query', 'like', '%'.$search.'%');

        $page = ($request->input('start') / $request->input('length')) + 1;
        $keywords = $model->paginate($request->input('length', 1), ['*'], 'page', $page);

        return $this->format($keywords);
    }

    private function format($keywords)
    {
        $collection = collect([]);

        foreach($keywords as $keyword){
            $data = [];

            $data['DT_RowId'] = $keyword->id;
            $data['query'] = $keyword->query;

            $data['top1'] = '';
            $data['top3'] = '';
            $data['top5'] = '';
            $data['top10'] = '';
            $data['top20'] = '';
            $data['top50'] = '';
            $data['top100'] = '';

            if($keyword->prices){
                foreach($keyword->prices->toArray() as $key => $price) {
                    if (isset($data[$key]))
                        $data[$key] = $price;
                }
            }

            $collection->push($data);
        }

        return $collection;
    }

    public function getDataTable()
    {
        $data = $this->getKeywordsWithPrice();

        $regions = collect([]);
        $searchengines = $this->regions->pluck('location.name', 'id');
        foreach($searchengines as $id => $name){
            $regions->push([
                'id' => $id,
                'name' => $name,
            ]);
        }

        $collection = collect([]);
        $collection->put('data', $data);
        $collection->put('regions', $regions);
        $collection->put('draw', $this->request->input('draw'));

        $records = $this->project->keywords->count();
        $collection->put('recordsFiltered', $records);
        $collection->put('recordsTotal', $records);

        return $collection;
    }
}
