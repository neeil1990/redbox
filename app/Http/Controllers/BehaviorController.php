<?php

namespace App\Http\Controllers;

use App\Behavior;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class BehaviorController extends Controller
{

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

    public function check()
    {
        return view('behavior.check');
    }

    public function verify(Request $request)
    {
        $this->validate($request, [
            'code' => ['required', 'min:6'],
        ]);

        $code = $request->input('code');

        try {
            $behavior = Behavior::where('status', 0)->where('code', $code)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return back()->withErrors(['code' => __('Code not found!')])->withInput();
        }

        $behavior->status = 1;
        $behavior->save();

        Session::flash('applied', __('Promo code applied!'));

        return back();
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
            'minutes' => ['required', 'between:1,60']
        ]);

        $domain = $request->input('domain');
        Auth::user()->behaviors()->create([
            'id' => $this->getId(),
            'code' => $this->getCodeByDomain($domain),
            'domain' => $domain,
            'minutes' => $request->input('minutes'),
        ]);

        return redirect()->route('behavior.index');
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
            $behavior->domain,
            $behavior->code,
            $behavior->minutes,
            '',
            '0',
            '0',
        ];
        $strArr = implode("||", $arParams);
        $params = base64_encode($strArr);

        return view('behavior.show', compact('behavior', 'params'));
    }

    public function destroy(Behavior $behavior)
    {
        $behavior->delete();

        return redirect()->route('behavior.index');
    }

}
