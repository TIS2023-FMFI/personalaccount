<?php

namespace Tests\Feature\FinancialAccount;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateAccountTest extends TestCase
{
    private $user, $account;

    private $ajaxHeaders;

    private $setupDone = false;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->setupDone) {
            return;
        }

        $this->user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
        $this->account = Account::factory()->for($this->user)->create();

        $this->ajaxHeaders = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];

        $this->setupDone = true;
    }

    public function test_that_unauthenticated_user_cannot_update_account()
    {
        $response = $this->put('/accounts/' . $this->account->id);

        $response
            ->assertStatus(302);
    }

    public function test_that_only_ajax_requests_are_handled()
    {
        $response = $this->actingAs($this->user)
                            ->put('/accounts/' . $this->account->id);

        $response
            ->assertStatus(500);
    }

    public function test_that_user_cannot_update_nonexisting_account()
    {
        $response = $this->actingAs($this->user)
                            ->withHeaders($this->ajaxHeaders)
                            ->put('/accounts/99999');

        $response
            ->assertStatus(404);
    }

    public function test_that_user_can_update_existing_account()
    {
        $updated =$this->account->getAttributes();
        $updated['title'] .= ' new';
        $updated['sap_id'] .= '-00';
        unset($updated['user_id']);

        $response = $this->actingAs($this->user)
                            ->withHeaders($this->ajaxHeaders)
                            ->put(
                                '/accounts/' . $this->account->id,
                                $updated
                            );

        $response
            ->assertStatus(200);

        $this->account->refresh();
        $this->assertEquals($updated['title'], $this->account->title);
        $this->assertEquals($updated['sap_id'], $this->account->sap_id);
    }

    public function test_that_user_can_update_title_only()
    {
        $updated =$this->account->getAttributes();
        $updated['title'] .= ' new';
        unset($updated['user_id']);

        $response = $this->actingAs($this->user)
            ->withHeaders($this->ajaxHeaders)
            ->put(
                '/accounts/' . $this->account->id,
                $updated
            );

        $response
            ->assertStatus(200);

        $this->account->refresh();
        $this->assertEquals($updated['title'], $this->account->title);
        $this->assertEquals($updated['sap_id'], $this->account->sap_id);
    }
}
