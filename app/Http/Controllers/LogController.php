<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = auth()->user()->authentications;
        $visitors = \App\Models\Visitor::orderBy('created_at', 'desc')->get();

        return view('dashboard.log', compact('logs', 'visitors'));
    }
}
