<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;

/**
 * A controller responsible for registering new users.
 * 
 * This controller provides methods to:
 *      - register a new user
 */
class RegisterController extends Controller
{
    /**
     * Handle a request to register a new user.
     * 
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * the request to handle
     * @return \Illuminate\Http\Response
     * a response containing the information about the result of this operation
     * presented as a plain-text message
     */
    public function register(RegisterRequest $request)
    {
        $email = $request->validated('email');

        try {
            User::create([ 'email' => $email ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response(trans('auth.register.failed'), 500);
        }

        return response(trans('auth.register.success'), 201);
    }
}
