<?php

namespace App\Http\Controllers;

use App\HttpHeader;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function httpHeaders($id, HttpHeader $header)
    {
        $response = $header->getData($id);
        return view('pages.headers', compact('response', 'id'));
    }
}
