<?php


namespace App\ViewComposers;

use App\Description;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
class DescriptionComposer
{

    public function compose(View $view)
    {
        $code = request()->path();

        $description = Description::where(['code' => $code, 'lang' => App::getLocale()])->get();

        $description = $description->filter(function ($value) {
            return (!is_null($value->description));
        });

        $description = $description->keyBy('position');

        $view->with(compact('code', 'description'));
    }
}
