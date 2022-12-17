<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManageAccountTest extends TestCase
{
    private $newPassword;

    public function setUp(): void
    {
        parent::setUp();

        $this->newPassword = trans('validation.attributes.new_password');
    }

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
                                [ 'new_password' => '', 'new_password_confirmation' => '' ]
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
                                [ 'new_password' => '', 'new_password_confirmation' => '' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.new_password.0',
                trans('validation.required', [ 'attribute' => $this->newPassword ])
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
                                [ 'new_password' => 'abc', 'new_password_confirmation' => '' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.new_password.0',
                trans('validation.confirmed', [ 'attribute' => $this->newPassword ])
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
                                [ 'new_password' => 'abc', 'new_password_confirmation' => 'abc' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.new_password.0',
                trans('validation.min.string', [ 'attribute' => $this->newPassword, 'min' => 8 ])
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
                                [ 'new_password' => str_repeat('a', 256), 'new_password_confirmation' => str_repeat('a', 256) ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.new_password.0',
                trans('validation.max.string', [ 'attribute' => $this->newPassword, 'max' => 255 ])
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
                                [ 'new_password' => '12345678', 'new_password_confirmation' => '12345678' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.new_password',
                fn ($errors) => in_array(
                    trans('validation.password.letters', [ 'attribute' => $this->newPassword ]),
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
                                [ 'new_password' => 'abcdefgh', 'new_password_confirmation' => 'abcdefgh' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.new_password',
                fn ($errors) => in_array(
                    trans('validation.password.mixed', [ 'attribute' => $this->newPassword ]),
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
                                [ 'new_password' => 'ABCdefgh', 'new_password_confirmation' => 'ABCdefgh' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.new_password',
                fn ($errors) => in_array(
                    trans('validation.password.numbers', [ 'attribute' => $this->newPassword ]),
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
                                [ 'new_password' => 'ABCdef78', 'new_password_confirmation' => 'ABCdef78' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.new_password',
                fn ($errors) => in_array(
                    trans('validation.password.symbols', [ 'attribute' => $this->newPassword ]),
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
                                [ 'new_password' => $password, 'new_password_confirmation' => $password ]
                            );

        $response
            ->assertStatus(200);

        $this->assertCredentials([
            'email' => 'a@b.c',
            'password' => $password,
        ]);
    }
}
