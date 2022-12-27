<?php

namespace Tests\Feature;

use App\Http\Controllers\FinancialAccounts\AccountDetailController;
use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AccountDetailTest extends TestCase
{
    use DatabaseTransactions;

    private int $perPage;

    public function setUp(): void
    {
        parent::setUp();
        $this->perPage = AccountDetailController::$perPage;
    }

    public function test_correct_view()
    {
        $user = User::firstOrCreate(['email' => 'a@b.c']);
        $account = Account::factory()->create(['user_id' => $user]);
        $response = $this->actingAs($user)->get(sprintf('/account/%d', $account->id));
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.account');
    }

    public function test_correct_view_data()
    {

        $user = User::create([ 'email' => 'new@b.c' ]);

        $account = Account::factory()->has(FinancialOperation::factory()->count(5))->create(['user_id' => $user]);

        $response = $this->actingAs($user)->get(sprintf('/account/%d', $account->id));
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.account');

        $data = $response->viewData('operations');

        $this->assertCount(5,$data);
    }

    public function test_pagination_is_used()
    {
        $user = User::create([ 'email' => 'new@b.c' ]);

        $count = $this->perPage;
        $account = Account::factory()->has(FinancialOperation::factory()->count($count + 1))->create(['user_id' => $user]);

        $response = $this->actingAs($user)->get(sprintf('/account/%d', $account->id));
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.account');

        $operations = $response->viewData('operations');

        $this->assertCount($count,$operations);

        $this->assertTrue($operations->hasPages());
        $this->assertEquals($count+1,$operations->total());
        $this->assertEquals(2,$operations->lastPage());

    }

    public function test_paging_second_page()
    {
        $user = User::create([ 'email' => 'new@b.c' ]);

        $count = $this->perPage;
        $account = Account::factory()->has(FinancialOperation::factory()->count($count + 1))->create(['user_id' => $user]);

        $response = $this->actingAs($user)->get(sprintf('/account/%d?page=2', $account->id));
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.account');

        $operations = $response->viewData('operations');

        $this->assertCount(1,$operations);
        $this->assertFalse($operations->hasMorePages());
    }

}
