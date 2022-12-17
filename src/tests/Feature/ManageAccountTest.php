<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManageAccountTest extends TestCase
{
    public function test_that_unauthenticated_user_cannot_change_password()
    {
        $response = $this->post('/change-password');

        $response
            ->assertStatus(302);
    }

    public function test_that_only_ajax_requests_are_handled()
    {
        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
 
        $response = $this->actingAs($user)
                            ->post(
                                '/change-password',
                                [ 'password' => '', 'password_confirmation' => '' ]
                            );

        $response
            ->assertStatus(500);
    }

    public function test_that_empty_password_is_rejected()
    {
        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
 
        $response = $this->actingAs($user)
                            ->withHeaders([ 
                                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                                'Accept' => 'application/json',
                            ])->post(
                                '/change-password',
                                [ 'password' => '', 'password_confirmation' => '' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.password.0',
                trans('validation.required', [ 'attribute' => 'password' ])
            );
    }

    public function test_that_unconfirmed_password_is_rejected()
    {
        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
 
        $response = $this->actingAs($user)
                            ->withHeaders([ 
                                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                                'Accept' => 'application/json',
                            ])->post(
                                '/change-password',
                                [ 'password' => 'abc', 'password_confirmation' => '' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.password.0',
                trans('validation.confirmed', [ 'attribute' => 'password' ])
            );
    }

    public function test_that_short_password_is_rejected()
    {
        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
 
        $response = $this->actingAs($user)
                            ->withHeaders([ 
                                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                                'Accept' => 'application/json',
                            ])->post(
                                '/change-password',
                                [ 'password' => 'abc', 'password_confirmation' => 'abc' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.password.0',
                trans('validation.min.string', [ 'attribute' => 'password', 'min' => 8 ])
            );
    }

    public function test_that_too_long_password_is_rejected()
    {
        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
 
        $response = $this->actingAs($user)
                            ->withHeaders([ 
                                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                                'Accept' => 'application/json',
                            ])->post(
                                '/change-password',
                                [ 'password' => str_repeat('a', 256), 'password_confirmation' => str_repeat('a', 256) ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.password.0',
                trans('validation.max.string', [ 'attribute' => 'password', 'max' => 255 ])
            );
    }

    public function test_that_password_without_letters_is_rejected()
    {
        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
 
        $response = $this->actingAs($user)
                            ->withHeaders([ 
                                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                                'Accept' => 'application/json',
                            ])->post(
                                '/change-password',
                                [ 'password' => '12345678', 'password_confirmation' => '12345678' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.password',
                fn ($errors) => in_array(
                    trans('validation.password.letters', [ 'attribute' => 'password' ]),
                    $errors
                )
            );
    }

    public function test_that_password_without_mixed_case_is_rejected()
    {
        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
 
        $response = $this->actingAs($user)
                            ->withHeaders([ 
                                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                                'Accept' => 'application/json',
                            ])->post(
                                '/change-password',
                                [ 'password' => 'abcdefgh', 'password_confirmation' => 'abcdefgh' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.password',
                fn ($errors) => in_array(
                    trans('validation.password.mixed', [ 'attribute' => 'password' ]),
                    $errors
                )
            );
    }

    public function test_that_password_without_numbers_is_rejected()
    {
        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
 
        $response = $this->actingAs($user)
                            ->withHeaders([ 
                                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                                'Accept' => 'application/json',
                            ])->post(
                                '/change-password',
                                [ 'password' => 'ABCdefgh', 'password_confirmation' => 'ABCdefgh' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.password',
                fn ($errors) => in_array(
                    trans('validation.password.numbers', [ 'attribute' => 'password' ]),
                    $errors
                )
            );
    }

    public function test_that_password_without_symbols_is_rejected()
    {
        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
 
        $response = $this->actingAs($user)
                            ->withHeaders([ 
                                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                                'Accept' => 'application/json',
                            ])->post(
                                '/change-password',
                                [ 'password' => 'ABCdef78', 'password_confirmation' => 'ABCdef78' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.password',
                fn ($errors) => in_array(
                    trans('validation.password.symbols', [ 'attribute' => 'password' ]),
                    $errors
                )
            );
    }

    public function test_that_strong_password_is_accepted()
    {
        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
        $password = 'ABCd@f78';
 
        $response = $this->actingAs($user)
                            ->withHeaders([ 
                                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                                'Accept' => 'application/json',
                            ])->post(
                                '/change-password',
                                [ 'password' => $password, 'password_confirmation' => $password ]
                            );

        $response
            ->assertStatus(200);

        $this->assertCredentials([
            'email' => 'a@b.c',
            'password' => $password,
        ]);
    }
}
