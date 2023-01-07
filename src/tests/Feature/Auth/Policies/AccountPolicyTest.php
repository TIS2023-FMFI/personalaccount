<?php

namespace Tests\Feature\Auth\Policies;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountPolicyTest extends TestCase
{
    private $otherUser;
    private $account;

    private $setupDone = false;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->setupDone) {
            return;
        }

        $this->otherUser = User::firstOrCreate([ 'email' => 'new@b.c' ]);

        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
        $this->account = Account::factory()->create([ 'user_id' => $user ]);

        $this->setupDone = true;
    }

    public function test_that_unauthorized_user_cannot_view_account()
    {
        $response = $this->actingAs($this->otherUser)
                            ->get('/account/' . $this->account->id);
        
        $response
            ->assertStatus(403);
    }

    public function test_that_unauthorized_user_cannot_download_operations_export()
    {
        $response = $this->actingAs($this->otherUser)
                            ->get('/export/' . $this->account->id);
        
        $response
            ->assertStatus(403);
    }
}
