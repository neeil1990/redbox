<?php

namespace App\Http\Controllers;

use App\Exports\MetaTagsHistoriesExport;
use App\Exports\MetaTagsCompareHistoriesExport;
use App\Mail\MetaTagsEmail;
use App\MetaTag;
use App\MetaTagsHistory;
use App\TelegramBot;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yangqi\Htmldom\Htmldom;
use Ixudra\Curl\Facades\Curl;

/**
 * Class MetaTagsController
 * @package App\Http\Controllers
 */
class MetaTagsController extends Controller
{
    protected $html;

    protected $tags = [
        ['name' => 'title', 'tag' => 'title', 'type' => 'string'],
        ['name' => 'description', 'tag' => 'meta[name=description]', 'type' => 'string'],
        ['name' => 'keywords', 'tag' => 'meta[name=keywords]', 'type' => 'string'],
        ['name' => 'canonical', 'tag' => 'link[rel=canonical]', 'type' => 'int'],
        ['name' => 'noindex', 'tag' => 'noindex', 'type' => 'int'],
        ['name' => 'robots', 'tag' => 'robots', 'type' => 'string'],
        ['name' => 'h1', 'tag' => 'h1', 'type' => 'string'],
        ['name' => 'h2', 'tag' => 'h2', 'type' => 'string'],
        ['name' => 'h3', 'tag' => 'h3', 'type' => 'string'],
        ['name' => 'a', 'tag' => 'a', 'type' => 'string'],
    ];

    protected $response;

    /**
     * MetaTagsController constructor.
     */
    public function __construct()
    {

        $this->middleware(['permission:Meta tags']);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export($id)
    {
        return Excel::download(new MetaTagsHistoriesExport($id), 'meta_tags.csv');
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCompare($id, $id_compare)
    {
        return Excel::download(new MetaTagsCompareHistoriesExport($id, $id_compare), 'meta_tags_compare.csv');
    }

    protected function lang() {

        return collect([
            'check_url' => __('Check URL'),
            'timeout_request' => __('Timeout request'),
            'length_word' => __('Length'),
            'title' => __('title (recommend 70-80)'),
            'description' => __('description (recommend 180-300)'),
            'keywords' => __('keywords'),
            'min' => __('Minimum'),
            'max' => __('Maximum'),
            'send' => __('Send'),
            'projects' => __('Projects'),
            'id' => __('ID'),
            'name' => __('Name'),
            'period' => __('Period'),
            'timeout' => __('Timeout'),
            'link' => __('Link'),
            'status' => __('Status'),
            'off' => __('Off'),
            'on' => __('On'),
            'history' => __('History'),
            'start' => __('Start'),
            'edit' => __('Edit'),
            'delete' => __('Delete'),
            'filter' => __('Filter'),
            'all' => __('All'),
            'done' => __('Done'),
            'text_analysis' => __('Text analysis'),
            'save_as_project' => __('Save as project'),
            'check_interval_every' => __('Check interval every'),
            'hours' => __('hours'),
            'save_project' => __('Save project'),
            'project_name' => __('Project name'),
            'close' => __('Close'),
            'save' => __('Save'),
            'export' => __('Export'),
            'tag' => __('Tag'),
            'content' => __('Content'),
            'count' => __('Count'),
            'main_problems' => __('Main problems'),
            'go_to_site' => __('Go to site'),
        ]);
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function index()
    {
        $meta = Auth::user()->metaTags()->latest()->get();

        $lang = $this->lang();

        return view('meta-tags.index', compact('meta', 'lang'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getMetaTags(Request $request) {

        $title = $request->input('url', false);
        $length = $request->input('length', false);

        return $this->dataMetaTags($title, $length);
    }

    /**
     * @param $title
     * @param $length
     * @return array
     */
    protected function dataMetaTags($title, $length)
    {
        $error = [];
        $recommend_length = [];

        foreach ($length as $len) {
            $recommend_length[$len['id'].'_min'] = $len['input']['min'];
            $recommend_length[$len['id'].'_max'] = $len['input']['max'];
        }

        $data = $this->domain($title)->get();

        foreach ($data as $tag => $value){
            $error['main'][$tag] = $this->errorsMetaTags($tag, $value, 'main', $recommend_length);


            if($this->response['status'] !== 200 && $tag === 'title'){
                $status = 'code:' . $this->response['status'];
                $error['badge'][$status] = [$this->templateErrors( __('Error') . ' ' . __('code') . ': ' . $this->response['status'], '')];
            }

            $error['badge'][$tag] = $this->errorsMetaTags($tag, $value, 'badge', $recommend_length);
        }

        return compact('title', 'data', 'error');
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function domain(string $domain)
    {
        $html = Curl::to($domain)->returnResponseArray()->get();

        $this->response = $html;

        $DOM = new Htmldom();
        $this->html = $DOM->load($html['content']);

        return $this;
    }

    /**
     * Get array http
     *
     * @return array
     */
    public function get()
    {
        $result = [];

        foreach ($this->tags as $tag){
            $result[$tag['name']] = $this->getByString($tag['tag']);
        }

        return $result;
    }

    public function getByString(string $tag)
    {
        $el = $this->html->find($tag);

        if(!$el)
            return false;

        $arr = [];
        foreach ($el as $e){

            if(strlen(trim($e->plaintext)) > 1)
                $arr[] = trim($e->plaintext);
            elseif(isset($e->attr['content']))
                $arr[] = trim($e->attr['content']);
            else
                $arr[] = trim($e->outertext);
        }

        return $arr;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $meta = Auth::user()->metaTags()->create($request->all((new MetaTag)->getFillable()));

        $this->storeHistories($request, $meta->id);

        return $meta;
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function storeHistories(Request $request, $id) {

        $history = $request->input('histories', false);

        if($history){
            $history_links = count($history);
            $history = collect($history)->toJson();

            MetaTagsHistory::create(['meta_tag_id' => $id, 'quantity' => $history_links, 'data' => $history]);
        }
    }

    /**
     * All histories by meta tags
     *
     * @param $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function showHistories($id) {

        $project = Auth::user()->metaTags()->find($id);

        $project->histories()->where([
            ['created_at', '<', Carbon::now()->subDays(90)],
            ['ideal', '=', 0]
        ])->delete();

        $project->histories->transform(function ($item, $key) {

            $errors = json_decode($item->data);

            $error_quantity = null;
            foreach ($errors as $e){
                if(isset($e->error)){
                    $arr_error = Arr::flatten($e->error->badge);
                    if(is_array($arr_error))
                        $error_quantity += count($arr_error);
                }
            }

            $item['error_quantity'] = $error_quantity;
            return $item;
        });

        return view('meta-tags.show', compact('project'));
    }

    /**
     * One history by meta tags
     *
     * @param $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     * @throws \ErrorException
     */
    public function showHistory($id){

        $history = MetaTagsHistory::findOrFail($id);

        if($history->project->user_id != Auth::id())
            throw new \ErrorException('User not valid');

        $project = $history->project;
        $data = collect(json_decode($history->data));

        $lang = $this->lang();

        return view('meta-tags.history', compact('data', 'project', 'lang'));
    }

    public function showHistoryCompare($id, $id_compare)
    {
        $response = [];

        $history = MetaTagsHistory::findOrFail($id);
        $history_compare = MetaTagsHistory::findOrFail($id_compare);

        if($history->project->user_id != Auth::id() || $history_compare->project->user_id != Auth::id())
            throw new \ErrorException('User not valid');

        $this->createCompareArray($history, 'card', $response);
        $this->createCompareArray($history_compare, 'card_compare', $response);

        $collection = collect($response);

        $filter = [];
        foreach ($response as $r){
            if(isset($r['badge'])){
                $tags = collect($r['badge'])->collapse()->keys();
                foreach($tags as $tag)
                    $filter[$tag] = $tag;
            }
        }

        return view('meta-tags.histories_compare', compact('collection', 'filter'));
    }

    protected function createCompareArray($model, $name = 'card', &$response = [])
    {
        $histories = json_decode($model->data);
        foreach ($histories as $item){
            $response[$item->title][$name]['id'] = $model->id;
            $response[$item->title][$name]['date'] = $model->created_at->format('d.m.Y');
            $response[$item->title][$name]['tags'] = $item->data;
            $response[$item->title][$name]['error'] = $item->error->main;

            foreach ($item->error->badge as $t => $b){
                if(count($b))
                    $response[$item->title]['badge'][$model->created_at->format('d.m.Y') . '(' . $model->id . ')'][$t] = $b;
            }
        }

        return $response;
    }

    /**
     * @param $tag
     * @param $val
     * @param array $recommend_length
     * @param $type
     * @return array
     */
    public function errorsMetaTags($tag, $val, $type, $recommend_length = array()) {

        if(empty($type))
            $type = 'main';

        $strSmall = '';
        $errors = [];

        if(is_array($val)){

            if(count($val) > 1 && ($tag === 'title' || $tag === 'description' || $tag === 'keywords' || $tag === 'canonical' || $tag === 'h1')){

                if($type === 'main')
                    $strSmall = __('Duplicate tag, Check the page and leave 1 tag');

                $errors[] = $this->templateErrors('< '.$tag.' > ' . count($val) . 'шт.', $strSmall);
            }
            elseif(count($val) === 1) {

                if(isset($recommend_length[$tag.'_min']) && $recommend_length[$tag.'_max']){

                    $min = $recommend_length[$tag.'_min'];
                    $max = $recommend_length[$tag.'_max'];

                    if($min && $max) {
                        if( strlen($val[0]) < $min || strlen($val[0]) > $max){

                            if($type === 'main')
                                $strSmall = __('You have set a range from') . ' '.$min.' '. __('to') .' '.$max;

                            $errors[] = $this->templateErrors( __('Length') . ' '.$tag.': '.strlen($val[0]), $strSmall);
                        }
                    }
                }
            }
        }

        if($type === 'main'){
            if(empty($errors))
                $errors[] = '<span class="badge badge-success">'. __('No problem') .'</span>';
        }

        return $errors;
    }

    protected function templateErrors($text, $smallText)
    {
        $str = '';

        if(strlen($text))
            $str .= '<span class="badge badge-danger mr-1">'.$text.'</span>';

        if(strlen($smallText))
            $str .= '<br/><small>'.$smallText.'</small>';

        return $str;
    }

    public function updateHistoriesIdeal(Request $request, $id) {

        $project = Auth::user()->metaTags()->find($id);
        $project->histories()->update(['ideal' => false]);

        $history_id = $request->input('id', false);
        $project->histories()->where('id', $history_id)->update(['ideal' => true]);

        return $history_id;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
       Auth::user()->metaTags()->find($id)->update($request->all((new MetaTag)->getFillable()));
       return Auth::user()->metaTags()->find($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Auth::user()->metaTags()->find($id)->delete();
    }

    public function destroyHistory($id)
    {
        $history = MetaTagsHistory::findOrFail($id);

        if($history->project->user_id != Auth::id())
            throw new \ErrorException('User not valid');

        $history->delete();
    }
}
