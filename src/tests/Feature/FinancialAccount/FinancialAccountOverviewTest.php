<?php

namespace Tests\Feature\FinancialAccount;

use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\OperationType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinancialAccountOverviewTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_accounts_for_user_are_retrieved()
    {
        $user = User::create([ 'email' => 'new@b.c' ]);
        Account::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->accounts);
    }

    public function test_correct_view(){

        $user = User::firstOrCreate(['email' => 'a@b.c']);
        $response = $this->actingAs($user)->get('/accounts');
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.index');
    }

    public function test_correct_view_data(){

        $user = User::create([ 'email' => 'new@b.c' ]);

        $account1 = Account::factory()->create(['user_id' => $user]);
        $account2 = Account::factory()->create(['user_id' => $user]);

        $incomeType = OperationType::factory()->create(['name' => 'income', 'expense' => false, 'lending' => false]);
        $expenseType = OperationType::factory()->create(['name' => 'expense', 'expense' => true, 'lending' => false]);

        FinancialOperation::factory()->create([
            'account_id' => $account1,
            'operation_type_id' => $incomeType,
            'sum' => 10]);
        FinancialOperation::factory()->create([
            'account_id' => $account2,
            'operation_type_id' => $expenseType,
            'sum' => 10]);

        $response = $this->actingAs($user)->get('/accounts');
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.index');

        $accounts = $response->viewData('accounts');

        $this->assertCount(2,$accounts);
        $this->assertEquals(10,$accounts->find($account1->id)->getBalance());
        $this->assertEquals(-10,$accounts->find($account2->id)->getBalance());
    }

}
