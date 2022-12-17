<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_that_first_user_sees_registration()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $response = $this->get('/login');

        $response
            ->assertStatus(200)
            ->assertViewIs('auth.first_user');
    }

    public function test_that_login_is_available()
    {
        User::firstOrCreate([ 'email' => 'a@b.c' ]);
        
        $response = $this->get('/login');

        $response
            ->assertStatus(200)
            ->assertViewIs('auth.login');
    }

    public function test_that_empty_email_is_rejected()
    {
        $response = $this->withHeader('Accept', 'application/json')
                            ->post(
                                '/login',
                                [ 'email' => '', 'password' => 'abc' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.email.0',
                trans('validation.required', [ 'attribute' => 'email' ])
            );
    }

    public function test_that_invalid_email_is_rejected()
    {
        $response = $this->withHeader('Accept', 'application/json')
                            ->post(
                                '/login',
                                [ 'email' => 'aaa', 'password' => 'abc' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.email.0',
                trans('validation.email', [ 'attribute' => 'email' ])
            );
    }

    public function test_that_empty_password_is_rejected()
    {
        $response = $this->withHeader('Accept', 'application/json')
                            ->post(
                                '/login',
                                [ 'email' => 'x@x.x', 'password' => '' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.password.0',
                trans('validation.required', [ 'attribute' => 'password' ])
            );
    }

    public function test_that_unknown_user_is_not_logged_in()
    {
        $this->get('/login');

        $response = $this->withHeader('Accept', 'application/json')
                            ->post(
                                '/login',
                                [ 'email' => 'x@x.x', 'password' => 'abc' ]
                            );

        $response
            ->assertStatus(302)
            ->assertLocation('/login');
    }

    public function test_that_incorrect_password_is_rejected()
    {
        $this->get('/login');

        $response = $this->withHeader('Accept', 'application/json')
                            ->post(
                                '/login',
                                [ 'email' => 'a@b.c', 'password' => 'abc' ]
                            );

        $response
            ->assertStatus(302)
            ->assertLocation('/login');
    }

    public function test_that_user_can_log_in_with_correct_credentials()
    {
        $password = 'password';

        $user = User::firstOrNew([ 'email' => 'a@b.c' ]);
        $user->password = Hash::make($password);
        $user->save();

        $response = $this->withHeader('Accept', 'application/json')
                            ->post(
                                '/login',
                                [ 'email' => $user->email, 'password' => $password ]
                            );

        $response
            ->assertStatus(302)
            ->assertLocation(RouteServiceProvider::HOME);

        $this->assertAuthenticatedAs($user);
    }
}
