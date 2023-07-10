<?php

namespace App\Http\Controllers;

use App\Behavior;
use App\BehaviorsPhrase;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class BehaviorController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:Behavior']);
    }

    /**
     * Id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->generate();
    }

    /**
     * Code
     *
     * @return String
     */
    public function getCodeByDomain($domain)
    {
        $prefix = Str::limit($domain, 3, '');
        return implode('_', [$prefix, $this->generate(6)]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $behaviors = Auth::user()->behaviors()->get();

        return view('behavior.index', compact('behaviors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        if($tariff = $user->tariff()){

            $count = $user->behaviors()->count();
            $tariff = $tariff->getAsArray();
            if (array_key_exists('behavior', $tariff['settings'])) {

                if($count >= $tariff['settings']['behavior']['value']){

                    if($tariff['settings']['behavior']['message'])
                        flash()->overlay(__($tariff['settings']['behavior']['message']), __('Error'))->error();

                    return redirect()->route('behavior.index');
                }
            }
        }

        return view('behavior.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'domain' => ['required', 'min:3'],
            'minutes' => ['required', 'between:1,60'],
            'clicks' => ['required', 'between:1,100'],
            'pages' => ['required', 'between:1,100'],
        ]);

        $domain = $request->input('domain');

        Auth::user()->behaviors()->create([
            'id' => $this->getId(),
            'domain' => $domain,
            'minutes' => $request->input('minutes'),
            'clicks' => $request->input('clicks'),
            'pages' => $request->input('pages'),
            'description' => $request->input('description'),
        ]);

        return redirect()->route('behavior.index');
    }

    public function updateProject(Request $request, Behavior $behavior)
    {
        $this->validate($request, [
            'domain' => ['required', 'min:3'],
            'minutes' => ['required', 'between:1,60'],
            'clicks' => ['required', 'between:1,100'],
            'pages' => ['required', 'between:1,100'],
        ]);

        $behavior->update($request->all());

        Session::flash('update_site_code', __('Update code on your site!'));

        return redirect()->route('behavior.show', [$behavior->id]);
    }

    /**
     * Get a new, random ID.
     *
     * @param int $length
     * @return string
     */
    protected function generate(int $length = 40)
    {
        return Str::random($length);
    }

    /**
     * Determine if this is a valid ID.
     *
     * @param  string  $id
     * @return bool
     */
    public function isValidId($id)
    {
        return is_string($id) && ctype_alnum($id) && strlen($id) === 40;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Behavior  $behavior
     * @return \Illuminate\Http\Response
     */
    public function show(Behavior $behavior)
    {
        $arParams = [
            'domain' => $behavior->domain,
            'minutes' => $behavior->minutes,
            'clicks' => $behavior->clicks,
            'pages' => $behavior->pages,
        ];

        $strArr = json_encode($arParams);
        $params = base64_encode($strArr);

        return view('behavior.show', compact('behavior', 'params'));
    }

    /**
     * @param Behavior $behavior
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function edit(Behavior $behavior)
    {
        return view('behavior.edit', compact('behavior'));
    }

    /**
     * @param Behavior $behavior
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function editProject(Behavior $behavior)
    {
        return view('behavior.edit_project', compact('behavior'));
    }

    /**
     * @param Request $request
     * @param Behavior $behavior
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Behavior $behavior)
    {
        $data = [];
        $phrases = $request->input('phrases');
        $count = $request->input('count');

        foreach ($phrases as $k => $phrase){
            if(strlen($phrase) > 3){
                for ($i = 1; $i <= $count[$k]; $i++) {
                    $data[] = $phrase;
                }
            }
        }
        $phrases = $data;
        shuffle($phrases);

        foreach ($phrases as $phrase){
            if(strlen($phrase) > 3)
                $behavior->phrases()->create([
                    'code' => $this->getCodeByDomain($behavior->domain),
                    'phrase' => $phrase
                ]);
        }

        return redirect()->route('behavior.show', [$behavior->id]);
    }

    public function destroy(Behavior $behavior)
    {
        $behavior->delete();

        return route('behavior.index');
    }

    public function phraseDestroy($phrase, BehaviorsPhrase $behaviorsPhrase)
    {
        $phrase = $behaviorsPhrase->findOrFail($phrase);
        if($phrase->behavior->user_id === Auth::id())
            $phrase->delete();
        else
            abort(403);
    }

    public function destroyPhrases(Behavior $behavior)
    {
        $behavior->phrases()->delete();
    }

    public function phraseSortUpdate($phrase, BehaviorsPhrase $behaviorsPhrase, Request $request)
    {
        $sort = $request->input('sort', null);

        if(!$sort)
            return;

        $phrase = $behaviorsPhrase->findOrFail($phrase);

        $phrase->sort = $sort;
        $phrase->save();
    }

    public function sortMixed(Behavior $behavior)
    {
        $phrases = $behavior->phrases()->sortOrder()->get();
        $count = $phrases->count();

        foreach ($phrases as $phrase){
            $rand = rand(1, $count);
            $phrase->update(['sort' => $rand]);
        }

        return redirect()->back();
    }
}
