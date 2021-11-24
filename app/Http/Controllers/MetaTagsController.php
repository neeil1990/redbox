<?php

namespace App\Http\Controllers;

use App\Mail\MetaTagsEmail;
use App\MetaTag;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yangqi\Htmldom\Htmldom;
use Ixudra\Curl\Facades\Curl;

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
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $url = $request->input('url', false);
            return [
                'title' => $url,
                'data' => $this->domain($url)->get()
            ];
        }

        $meta = Auth::user()->metaTags()->get();

        return view('meta-tags.index', compact('meta'));
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
        $history = $request->input('result', false);

        $meta = Auth::user()->metaTags()->create($request->all((new MetaTag)->getFillable()));

        if($history){
            $history_links = count($history);


        }

        return $meta;
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
}
