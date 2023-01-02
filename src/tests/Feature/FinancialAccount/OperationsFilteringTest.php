<?php

namespace Tests\Feature\FinancialAccount;

use App\Http\Controllers\FinancialAccounts\AccountDetailController;
use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


/**
 * These tests must be run on a seeded database, as they generate plenty of models with foreign keys.
 */
class OperationsFilteringTest extends TestCase
{
    use DatabaseTransactions;

    private int $perPage;
    private array $dates;
    private $user;
    private $account;

    public function setUp(): void
    {
        parent::setUp();

        $this->perPage = AccountDetailController::$perPage;
        $this->dates = ['2000-01-01', '2001-01-01', '2002-01-01', '2003-01-01', '2004-01-01','2005-01-01'];
        $this->user = User::create([ 'email' => 'new@b.c' ]);
        $this->account = Account::factory()->create(['user_id' => $this->user]);

    }

    public function test_operations_between()
    {
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[0]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[1]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[2]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[3]]);

        $this->assertCount(4, $this->account->financialOperations);
        $this->assertCount(4, $this->account->operationsBetween($this->dates[0], $this->dates[3])->get());
        $this->assertCount(2, $this->account->operationsBetween($this->dates[0], $this->dates[1])->get());
        $this->assertCount(0, $this->account->operationsBetween($this->dates[4], $this->dates[5])->get());

    }

    public function test_invalid_operations_between()
    {
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[0]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[1]]);

        $this->assertCount(2, $this->account->financialOperations);
        $this->assertCount(0, $this->account->operationsBetween($this->dates[1], $this->dates[0])->get());

    }

    public function test_filtered_view_with_all_data()
    {
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[0]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[1]]);

        $response = $this->followingRedirects()->actingAs($this->user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                sprintf('/account/%d', $this->account->id),
                [
                    'date_from' => $this->dates[0],
                    'date_to' => $this->dates[1]
                ]
            );

        $response->assertStatus(200)
            ->assertViewIs('finances.account');

        $this->assertCount(2,$response->viewData('operations'));
    }

    public function test_filtered_view_with_some_data()
    {
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[0]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[1]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[2]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[3]]);

        $response = $this->followingRedirects()->actingAs($this->user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                sprintf('/account/%d', $this->account->id),
                [
                    'date_from' => $this->dates[1],
                    'date_to' => $this->dates[2],
                ]
            );

        $response->assertStatus(200)
            ->assertViewIs('finances.account');

        $this->assertCount(2,$response->viewData('operations'));
    }

    public function test_filtered_view_with_no_data()
    {
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[0]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[1]]);

        $response = $this->followingRedirects()->actingAs($this->user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                sprintf('/account/%d', $this->account->id),
                [
                    'date_from' => $this->dates[2],
                    'date_to' => $this->dates[3],
                ]
            );

        $response->assertStatus(200)
            ->assertViewIs('finances.account');

        $this->assertCount(0,$response->viewData('operations'));
    }

    public function test_view_data_unbound_on_both_sides()
    {
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[0]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[1]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[2]]);

        $response = $this->followingRedirects()->actingAs($this->user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                sprintf('/account/%d', $this->account->id),
                [
                    'date_from' => null,
                    'date_to' => null
                ]
            );

        $response->assertStatus(200)
            ->assertViewIs('finances.account');

        $this->assertCount(3,$response->viewData('operations'));
    }

    public function test_view_data_unbound_from_right()
    {
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[0]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[1]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[2]]);

        $response = $this->followingRedirects()->actingAs($this->user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                sprintf('/account/%d', $this->account->id),
                [
                    'date_from' => $this->dates[1],
                    'date_to' => null
                ]
            );

        $response->assertStatus(200)
            ->assertViewIs('finances.account');

        $this->assertCount(2,$response->viewData('operations'));
    }

    public function test_view_data_unbound_from_left()
    {
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[0]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[1]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[2]]);

        $response = $this->followingRedirects()->actingAs($this->user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                sprintf('/account/%d', $this->account->id),
                [
                    'date_from' => null,
                    'date_to' => $this->dates[1]
                ]
            );

        $response->assertStatus(200)
            ->assertViewIs('finances.account');

        $this->assertCount(2,$response->viewData('operations'));
    }

    public function test_filtering_invalid_interval()
    {
        $response = $this->followingRedirects()->actingAs($this->user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                sprintf('/account/%d', $this->account->id),
                [
                    'date_from' => $this->dates[1],
                    'date_to' => $this->dates[0],
                ]
            );

        $response->assertStatus(422);
    }

    public function test_filtering_invalid_input()
    {

        $response = $this->followingRedirects()->actingAs($this->user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                sprintf('/account/%d', $this->account->id),
                [
                    'date_from' => $this->dates[1],
                    'date_to' => 'test',
                ]
            );

        $response->assertStatus(422);
    }

    public function test_pagination_is_used_with_filtered_data()
    {

        $count = $this->perPage;
        FinancialOperation::factory()->count($count)->create(['account_id' => $this->account, 'date' => $this->dates[0]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[1]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[2]]);

        $response = $this->followingRedirects()->actingAs($this->user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                sprintf('/account/%d', $this->account->id),
                [
                    'date_from' => $this->dates[0],
                    'date_to' => $this->dates[1],
                ]
            );

        $response->assertStatus(200)
            ->assertViewIs('finances.account');
        $operations = $response->viewData('operations');

        $this->assertCount($count,$operations);
        $this->assertTrue($operations->hasPages());
        $this->assertEquals($count+1,$operations->total());
        $this->assertEquals(2,$operations->lastPage());
    }

    public function test_second_page_with_filtered_data()
    {

        $count = $this->perPage;
        FinancialOperation::factory()->count($count)->create(['account_id' => $this->account, 'date' => $this->dates[0]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[1]]);
        FinancialOperation::factory()->create(['account_id' => $this->account, 'date' => $this->dates[2]]);

        $response = $this->followingRedirects()->actingAs($this->user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                sprintf('/account/%d', $this->account->id),
                [
                    'date_from' => $this->dates[0],
                    'date_to' => $this->dates[1],
                ]
            );

        $url = $response->viewData('operations')->url(2);
        $response = $this->actingAs($this->user)->get($url);

        $response
            ->assertStatus(200)
            ->assertViewIs('finances.account');
        $operations = $response->viewData('operations');

        $this->assertCount(1,$operations);
        $this->assertFalse($operations->hasMorePages());
    }

}
