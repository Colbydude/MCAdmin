<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RouterController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if (auth()->guest()) {
            return redirect()->route('login');
        }

        return view('layouts.app', [
            'user' => $request->user()
        ]);
    }
}
