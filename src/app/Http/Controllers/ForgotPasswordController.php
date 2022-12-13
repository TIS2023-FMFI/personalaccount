<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendLoginLinkRequest;
use App\Mail\LoginLink;
use App\Models\LoginToken;
use Illuminate\Support\Facades\Mail;
use \App\Models\User;

class ForgotPasswordController extends Controller
{
    /**
     * Show the Forgot Password view.
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show()
    {
        return view('auth.forgot_password', [
            'message' => null
        ]);
    }

    /**
     * Send an email containing a unique login link to the provided email address.
     * 
     * @param SendLoginLinkRequest $request
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function sendLoginLink(SendLoginLinkRequest $request)
    {
        $email = $request->validated()['email'];
        $user = User::whereEmail($email)->first();
 
        if ($user) {
            $token = LoginToken::generate($user);

            Mail::to($user->email)->queue(new LoginLink($token->token, $token->valid_until));

            // TODO decide on how to deliver this message to the user
            // TODO prevent from submitting the form twice
            return back()->with([
                'message' => trans('auth.login-link-sent')
            ]);
        }
 
        return back()->withErrors([
            'email' => trans('auth.failed'),
        ])->onlyInput('email');
    }
}
