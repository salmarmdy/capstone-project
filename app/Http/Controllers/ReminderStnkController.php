<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReminderStnkController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.reminder.stnk');
    }
}
