<?php


namespace App\Classes\Cron;


use App\Http\Controllers\MetaTagsController;
use App\Mail\MetaTagsEmail;
use App\MetaTag;
use App\MetaTagsHistory;
use App\TelegramBot;
use App\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Services\TelegramBotService;

class MetaTags extends MetaTagsController
{
    protected $period;


    public function __construct($period)
    {
        $this->period = $period;
    }

    /**
     * @throws \Throwable
     */
    public function __invoke()
    {
        $models = MetaTag::where('period', $this->period)
            ->where('status', true)
            ->get();

        if($models->isEmpty())
            return;

        foreach($models as $model){

            $ideal = $model->histories()->where('ideal', true)->first();

            if(!$ideal)
                continue;

            $history = [];
            $links = preg_split('/\n|\r\n?/', $model->links);
            foreach ($links as $link){

                $meta = [];

                $meta['url'] = $link;

                $meta['length'][] = [
                    'id' => 'title',
                    'name' => '',
                    'input' => [
                        'min' => $model->title_min,
                        'max' => $model->title_max
                    ],
                ];

                $meta['length'][] = [
                    'id' => 'description',
                    'name' => '',
                    'input' => [
                        'min' => $model->description_min,
                        'max' => $model->description_max
                    ],
                ];

                $meta['length'][] = [
                    'id' => 'keywords',
                    'name' => '',
                    'input' => [
                        'min' => $model->keywords_min,
                        'max' => $model->keywords_max
                    ],
                ];

                $history[] = $this->dataMetaTags($meta['url'], $meta['length']);
            }

            if($history){
                $history_links = count($history);
                $history = collect($history)->toJson();

                $history = MetaTagsHistory::create(['meta_tag_id' => $model->id, 'quantity' => $history_links, 'data' => $history]);

                $compare = [];

                $this->createCompareArray($history, 'card', $compare);
                $this->createCompareArray($ideal, 'card_compare', $compare);

                $diff = [];
                foreach ($compare as $c) {

                    foreach ($c['card']['tags'] as $name => $val){

                        if(isset($c['card_compare']['tags']->$name)){

                            if($c['card_compare']['tags']->$name !== $val)
                                $diff[] = $val;
                        }
                    }
                }

                if(count($diff) && $model->user->telegram_bot_active){

                    App::setLocale($model->user->lang);

                    //send telegram notification
                    $link_compare = route('meta.history.compare', [$ideal->id, $history->id]);
                    $telegram = view('meta-tags.telegram', compact('model', 'link_compare'))->render();
                    
                    (new TelegramBotService($model->user->chat_id))->sendMsg($telegram);
                }
            }
        }

    }

}
