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
        $description = Description::where(['code' => $code, 'lang' => App::getLocale()])->first();

        if(is_object($description))
            $description = (strip_tags($description->description)) ? $description : null;

        $view->with(compact('code', 'description'));
    }
}
