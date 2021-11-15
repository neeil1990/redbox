<?php


namespace App\Classes\Cron;


use App\Http\Controllers\MetaTagsController;
use App\Mail\MetaTagsEmail;
use App\MetaTag;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MetaTags extends MetaTagsController
{
    protected $period;
    protected $file = 'html/meta_tags.html';

    /**
     * @return mixed
     */
    public function getPath()
    {
        return storage_path('app/public/' . $this->file);
    }

    public function __construct($period)
    {
        $this->period = $period;
    }

    /**
     * @throws \Throwable
     */
    public function __invoke()
    {
        $models = MetaTag::where('period', $this->period)->get();

        if($models->isEmpty())
            return;

        foreach($models as $model){
            $project = ['name' => '', 'data' => []];

            $arLinks = preg_split("/\r\n|\n|\r/", $model->links);

            $project['name'] = $model->name;
            foreach ($arLinks as $arLink)
                $project['data'][$arLink] = $this->domain($arLink)->get();

            $html = view('meta-tags.email', ['project' => $project])->render();

            Storage::put($this->file, $html);

            Mail::to(User::findOrFail($model->user_id))->send(new MetaTagsEmail($project['name'], $this->getPath()));
        }
    }

}
