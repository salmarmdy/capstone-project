<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StnkController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.stnk.index');
    }
}
