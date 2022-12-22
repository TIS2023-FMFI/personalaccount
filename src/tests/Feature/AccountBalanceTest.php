<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\OperationType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AccountBalanceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_zero_balance_with_no_operations()
    {
        $account = Account::factory()->create();
        $this->assertEquals(0,$account->getBalance());
    }

    public function test_positive_balance()
    {
        $account = Account::factory()->create();
        $gain = OperationType::factory()->create(['name' => 'testGain', 'expense' => '0']);

        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $gain,
            'sum' => 10]);

        $this->assertCount(1, $account->financialOperations);
        $this->assertEquals(10,$account->getBalance());
    }

    public function test_negative_balance()
    {
        $account = Account::factory()->create();
        $expense = OperationType::factory()->create(['name' => 'testExpense', 'expense' => '1']);

        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $expense,
            'sum' => 10]);

        $this->assertCount(1, $account->financialOperations);
        $this->assertEquals(-10,$account->getBalance());
    }

    public function test_balance_with_multiple_operations()
    {
        $account = Account::factory()->create();
        $gain = OperationType::factory()->create(['name' => 'testGain', 'expense' => '0']);
        $expense = OperationType::factory()->create(['name' => 'testExpense', 'expense' => '1']);

        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $gain,
            'sum' => 10]);
        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $expense,
            'sum' => 10]);

        $this->assertCount(2, $account->financialOperations);
        $this->assertEquals(0,$account->getBalance());
    }

    public function test_balance_with_multiple_operations_2()
    {
        $account = Account::factory()->create();
        $gain = OperationType::factory()->create(['name' => 'testGain', 'expense' => '0']);
        $expense = OperationType::factory()->create(['name' => 'testExpense', 'expense' => '1']);

        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $gain,
            'sum' => 500]);
        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $expense,
            'sum' => 250.50]);
        FinancialOperation::factory()->create([
            'account_id' => $account,
            'operation_type_id' => $expense,
            'sum' => 385.95]);

        $this->assertCount(3, $account->financialOperations);
        $this->assertEquals(-136.45,$account->getBalance());
    }

}
