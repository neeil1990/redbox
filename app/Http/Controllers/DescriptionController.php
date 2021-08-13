<?php

namespace App\Http\Controllers;

use App\Description;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DescriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($code, $position)
    {
        $description = $this->createOrGet($code, $position);

        return view('description.edit', compact('description'));
    }

    /**
     * Store or update a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($code, Request $request)
    {
        $description = $this->createOrGet($code, $request->input('position'));

        $description->user_id = Auth::id();

        $descInput = $request->input('description');
        $description->description = (strip_tags($descInput)) ? $descInput : null;

        $description->save();

        if($request->ajax())
            return $description;

        return redirect($description->code);
    }

    private function createOrGet($code, $position)
    {
        return Description::firstOrNew([
            'code' => $code,
            'lang' => App::getLocale(),
            'position' => $position,
        ]);
    }
}
