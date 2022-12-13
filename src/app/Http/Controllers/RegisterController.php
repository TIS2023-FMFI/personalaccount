<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Show the Register view.
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show()
    {
        return view('auth.register');
    }

    /**
     * Handle an attempt to register a new user, given their email address.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function register(Request $request)
    {
        // TODO not implemented
    }
}
