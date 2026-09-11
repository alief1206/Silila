<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = auth()->user()->authentications;

        return view('dashboard.log', compact('logs'));
    }
}
