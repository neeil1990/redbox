<?php

namespace App\Http\Controllers;

use App\Behavior;
use App\HttpHeader;
use App\MainProject;
use App\VisitStatistic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PublicController extends Controller
{
    public function httpHeaders($id, HttpHeader $header)
    {
        $lang = \request('lang', $header->lang);
        $response = $header->getData($id);
        return view('pages.headers', compact('response', 'id', 'lang'));
    }

    public function checkBehavior($id)
    {
        $behavior = Behavior::findOrFail($id);
        $phrases = $behavior->phrases()->where('status', 0)->sortOrder()->first();

        if (!$phrases)
            return view('behavior.errors.task');

        $arDomain = explode('.', $behavior->domain);
        $domain = preg_replace('/(.)./iu', '$1*', $arDomain[0]);
        $domain = implode('.', [$domain, $arDomain[1]]);

        return view('behavior.check', compact('phrases', 'domain', 'behavior'));
    }

    public function verifyBehavior(Request $request)
    {
        $this->validate($request, [
            'code' => ['required', 'min:6'],
        ]);

        $domain = $request->input('domain');
        $code = $request->input('code');

        try {
            $behavior = Behavior::where('domain', $domain)->firstOrFail();
            $phrases = $behavior->phrases()->where('status', 0)->sortOrder()->firstOrFail();
            if($phrases['code'] !== $code)
                throw new ModelNotFoundException();
        } catch (ModelNotFoundException $e) {
            return back()->withErrors(['code' => __('Code not found!')])->withInput();
        }

        $phrases->status = 1;
        $phrases->save();

        Session::flash('applied', __('Promo code applied!'));

        return back();
    }

    public function codeBehavior($site)
    {
        header('Access-Control-Allow-Origin: *');

        $behavior = Behavior::where('domain', $site)->firstOrFail();
        $phrases = $behavior->phrases()->where('status', 0)->sortOrder()->firstOrFail();
        return $phrases;
    }

    public function updateStatistics(Request $request): JsonResponse
    {
        $targetController = explode('@', $request->controllerAction)[0];
        $project = MainProject::Where('controller', 'like', "%" . $targetController . '@%')->first();

        if (isset($project)) {
            VisitStatistic::where('project_id', $project->id)
                ->where('user_id', Auth::id())
                ->where('date', Carbon::now()->toDateString())
                ->increment('seconds', $request->seconds);
        }

        return response()->json([], 200);
    }
}
