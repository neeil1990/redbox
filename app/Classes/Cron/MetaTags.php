<?php


namespace App\Classes\Cron;


use App\Http\Controllers\MetaTagsController;
use App\Mail\MetaTagsEmail;
use App\MetaTag;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MetaTags extends MetaTagsController
{
    protected $period;
    protected $path_pdf = 'app/public/pdf/meta_tags.pdf';

    public function __construct($period)
    {
        $this->period = $period;
    }


    public function __invoke()
    {
        $PDF = \App::make('dompdf.wrapper');
        $file = storage_path($this->path_pdf);

        $models = MetaTag::where('period', $this->period)->get();

        if($models->isEmpty())
            return;

        foreach($models as $model){
            $project = ['name' => '', 'data' => []];

            $arLinks = preg_split("/\r\n|\n|\r/", $model->links);

            $project['name'] = $model->name;
            foreach ($arLinks as $arLink)
                $project['data'][$arLink] = $this->domain($arLink)->get();

            $html = view('meta-tags.pdf', ['project' => $project])->render();

            $PDF->loadHTML($html)->setPaper('a4', 'landscape')->save($file);

            Mail::to(User::findOrFail($model->user_id))->send(new MetaTagsEmail(Str::snake($project['name'], '_'), $file));
        }
    }

}
