<?php

namespace App\Http\Controllers;

use App\Exports\MetaTagsHistoriesExport;
use App\Mail\MetaTagsEmail;
use App\MetaTag;
use App\MetaTagsHistory;
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

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export($id)
    {
        return Excel::download(new MetaTagsHistoriesExport($id), 'meta_tags.csv');
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function index()
    {
        $meta = Auth::user()->metaTags()->get();

        return view('meta-tags.index', compact('meta'));
    }

    public function getMetaTags(Request $request) {

        $error = [];

        $title = $request->input('url', false);
        $length = $request->input('length', false);

        $recommend_length = [];
        foreach ($length as $len) {
            $recommend_length[$len['id'].'_min'] = $len['input']['min'];
            $recommend_length[$len['id'].'_max'] = $len['input']['max'];
        }

        $data = $this->domain($title)->get();
        foreach ($data as $tag => $value){
            $error['main'][$tag] = $this->errorsMetaTags($tag, $value, 'main', $recommend_length);
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

    public function showHistory($id){

        $history = MetaTagsHistory::findOrFail($id);

        if($history->project->user_id != Auth::id())
            throw new \ErrorException('User not valid');

        $project = $history->project;
        $data = collect(json_decode($history->data));

        return view('meta-tags.history', compact('data', 'project'));
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
                    $strSmall = 'Дублирующийся тег, Проверьте страницу и оставьте 1 тег';

                $errors[] = $this->templateErrors('< '.$tag.' > ' . count($val) . 'шт.', $strSmall);
            }
            elseif(count($val) === 1) {

                if(isset($recommend_length[$tag.'_min']) && $recommend_length[$tag.'_max']){

                    $min = $recommend_length[$tag.'_min'];
                    $max = $recommend_length[$tag.'_max'];

                    if($min && $max) {
                        if( strlen($val[0]) < $min || strlen($val[0]) > $max){

                            if($type === 'main')
                                $strSmall = 'Вы задали диапазон с '.$min.' до '.$max;

                            $errors[] = $this->templateErrors('Длина '.$tag.': '.strlen($val[0]), $strSmall);
                        }
                    }
                }
            }
        }

        if($type === 'main'){
            if(empty($errors))
                $errors[] = '<span class="badge badge-success">Без проблем</span>';
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
