<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SelfCheckController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.employee.self-check.index');
    }
}
