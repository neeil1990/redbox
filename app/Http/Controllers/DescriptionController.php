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
    public function edit(Description $description)
    {
        return view('description.edit', compact('description'));
    }

    /**
     * Store or update a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Description $description, Request $request)
    {
        $description->user_id = Auth::id();
        $description->description = $request->input('description');

        $description->save();

        if($request->ajax())
            return $description;

        return redirect($description->code);
    }
}
