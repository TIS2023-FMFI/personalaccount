<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\OperationType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinancialAccountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_accounts_for_user_are_retrieved()
    {
        $user = User::create([ 'email' => 'new@b.c' ]);
        Account::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->accounts);
    }

    public function test_request_failure_without_required_fields()
    {
        $user = User::create([ 'email' => 'new@b.c' ]);

        $response = $this->actingAs($user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                '/create_account',
                [
                    'title' => '',
                    'sap_id' => '',
                ]
            );

        $response->assertStatus(422);
        $this->assertDatabaseMissing('accounts', [
            'user_id' => $user->id,
        ]);
        $user->refresh();
        $this->assertCount(0, $user->accounts);
    }

    public function test_new_account_is_created()
    {
        $user = User::create([ 'email' => 'new@b.c' ]);

        $this->assertCount(0, $user->accounts);

        $response = $this->actingAs($user)
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])->post(
                '/create_account',
                [
                    'title' => 'title',
                    'sap_id' => 'ID-123',
                ]
            );

        $response->assertStatus(201);
        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'title' => 'title',
            'sap_id' => 'ID-123'
        ]);

        $user->refresh();
        $this->assertCount(1, $user->accounts);
    }

    public function test_correct_view(){

        $user = User::firstOrCreate(['email' => 'a@b.c']);
        $response = $this->actingAs($user)->get('/');
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.index');
    }

    public function test_correct_view_data(){

        $user = User::create([ 'email' => 'new@b.c' ]);

        $account1 = Account::factory()->create(['user_id' => $user]);
        $account2 = Account::factory()->create(['user_id' => $user]);

        $gain = OperationType::factory()->create(['expense' => '0']);
        $expense = OperationType::factory()->create(['expense' => '1']);

        FinancialOperation::factory()->create([
            'account_id' => $account1,
            'operation_type_id' => $gain,
            'sum' => 10]);
        FinancialOperation::factory()->create([
            'account_id' => $account2,
            'operation_type_id' => $expense,
            'sum' => 10]);

        $response = $this->actingAs($user)->get('/');
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.index');

        $data = $response->viewData('accounts');

        $this->assertCount(2,$data);
        $this->assertEquals(10,$data[0]->getBalance());
        $this->assertEquals(-10,$data[1]->getBalance());
    }

}
