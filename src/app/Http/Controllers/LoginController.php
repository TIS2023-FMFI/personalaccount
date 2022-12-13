<?php

namespace App\Http\Controllers;

use App\Models\LoginToken;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the Login view.
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show()
    {
        return view('auth.login');
    }

    /**
     * Handle an attempt to login via email and password.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
 
            return redirect()->intended(RouteServiceProvider::HOME);
        }
 
        return back()->withErrors([
            'email' => trans('auth.failed'),
        ])->onlyInput('email');
    }

    /**
     * Handle an attempt to login via a login token.
     * 
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function loginUsingToken(Request $request, string $token)
    {
        $token = LoginToken::where('token', $token)->first();
 
        if ($token && $token->isValid()) { // TODO add: && $request->hasValidSignature()
            Auth::login($token->user);
            $token->invalidate();
 
            return redirect(RouteServiceProvider::HOME);
        }

        return view('auth.login_link_invalid');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        Auth::logout();
    
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect(route('login'));
    }
}
