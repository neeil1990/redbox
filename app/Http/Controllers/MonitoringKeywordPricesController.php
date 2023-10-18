<?php

namespace App\Http\Controllers;

use App\MonitoringKeywordPrice;
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

    private function initField(Request $request): void
    {
        $user = $this->user;
        $this->project = $user->monitoringProjects()->find($request['id']);
        if(!$this->project)
            abort('404');

        $this->regions = $this->project->searchengines()->with('location')->get();
        $this->request = $request;
    }

    public function index(Request $request)
    {
        $this->initField($request);

        if($request->ajax())
            return $this->getDataTable();

        $project = $this->project;

        return view('monitoring.price.index', compact('project'));
    }

    public function action(Request $request)
    {
        $this->initField($request);

        switch ($request->input('action')) {
            case 'edit':
                return $this->updateOrCreate();
                break;
        }
    }

    public function updateOrCreate()
    {
        $request = $this->request;

        $region = $request->input('region', null);
        $data = $request->input('data', []);

        foreach ($data as $id => $val){
            $collect = collect($val)->filter();
            MonitoringKeywordPrice::updateOrCreate(
                ['monitoring_keyword_id' => $id, 'monitoring_searchengine_id' => $region],
                $collect->toArray()
            );
        }

        return collect([
            'data' => []
        ]);
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

            if($keyword->price){
                foreach($keyword->price->toArray() as $key => $price) {
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
        $request = $this->request;
        $region = $request->input('region', $this->regions->first()['id']);

        $model = $this->project->keywords()->with(['price' => function($query) use ($region){
            $query->where('monitoring_searchengine_id', $region);
        }]);

        if($search = $request->input('search')['value'])
            $model->where('query', 'like', '%'.$search.'%');

        $page = ($request->input('start') / $request->input('length')) + 1;
        $keywords = $model->paginate($request->input('length', 1), ['*'], 'page', $page);

        $data = $this->format($keywords);

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

        $records = $keywords->total();
        $collection->put('recordsFiltered', $records);
        $collection->put('recordsTotal', $records);

        return $collection;
    }

    public function storeBudget(Request $request)
    {
        if($this->user->monitoringProjects()->find($request['id'])->update(['budget' => $request['budget']]))
            return "success";

        return "fail";
    }
}
