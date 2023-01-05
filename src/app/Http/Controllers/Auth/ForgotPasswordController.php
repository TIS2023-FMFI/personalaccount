<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\DatabaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendLoginLinkRequest;
use App\Mail\LoginLink;
use App\Models\LoginToken;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use \App\Models\User;
use \Exception;

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
        
        try {
            $user = $this->findUserByEmail($email);
            $this->sendLoginLinkToUser($user);
        } catch (DatabaseException $e) {
            return response($e->getMessage(), 500);
        } catch (QueryException $e) {
            return response(trans('auth.login-link.generation.failed'), 500);
        } catch (Exception $e) {
            return response(trans('auth.login-link.sending.failed'), 500);
        }

        return response(trans('auth.login-link.sending.success'));
    }

    /**
     * Find a user by their email address.
     * 
     * @param string $email
     * the email address identifying the user
     * @throws \App\Exceptions\DatabaseException
     * thrown if no user was found
     * @return \App\Models\User
     * the user identified by the provided email address
     */
    private function findUserByEmail(string $email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new DatabaseException(trans('auth.login-link.generation.failed'));
        }

        return $user;
    }

    /**
     * Send a login link to a user.
     * 
     * @param User $user
     * the user to whom to send the login link
     * @throws \App\Exceptions\DatabaseException
     * thrown if an unspecified database error ocurred during the creation of a
     * login token that should be embedded in the login link
     * @return void
     */
    private function sendLoginLinkToUser(User $user)
    {
        $token = LoginToken::generate($user);

        if (!$token->exists) {
            throw new DatabaseException(trans('auth.login-link.generation.failed'));
        }

        Mail::to($user->email)->queue(
            new LoginLink($token->token, $token->valid_until)
        );
    }
}
