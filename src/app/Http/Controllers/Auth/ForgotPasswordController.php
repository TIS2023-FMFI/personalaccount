<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendLoginLinkRequest;
use App\Mail\LoginLink;
use App\Models\LoginToken;
use Illuminate\Support\Facades\Mail;
use \App\Models\User;

/**
 * A controller responsible for handling the situations when a user has forgotten
 * their password.
 * 
 * This controller provides methods to:
 *      - show the forgot-password form
 *      - send a login link to a user
 */
class ForgotPasswordController extends Controller
{
    /**
     * Show the Forgot Password view.
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * the view that will be shown
     */
    public function show()
    {
        return view('auth.forgot_password');
    }

    /**
     * Handle a request to send a login link to a user's email address.
     * 
     * @param \App\Http\Requests\Auth\SendLoginLinkRequest $request
     * the request to handle
     * @return \Illuminate\Http\Response
     * a response containing the information about the result of this operation
     * presented as a plain-text message
     */
    public function sendLoginLink(SendLoginLinkRequest $request)
    {
        $email = $request->validated('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response(trans('auth.login-link.generation.failed'), 500);
        }

        try {
            $token = LoginToken::generate($user);

            Mail::to($user->email)->queue(
                new LoginLink($token->token, $token->valid_until)
            );
        } catch (\Illuminate\Database\QueryException $e) {
            return response(trans('auth.login-link.generation.failed'), 500);
        } catch (\Exception $e) {
            return response(trans('auth.login-link.sending.failed'), 500);
        }

        return response(trans('auth.login-link.sending.success'));
    }
}
