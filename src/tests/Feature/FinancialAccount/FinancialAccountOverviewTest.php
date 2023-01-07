<?php

namespace Tests\Feature\FinancialAccount;

use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\OperationType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class FinancialAccountOverviewTest extends TestCase
{
    use DatabaseTransactions;

    private $titleAttr;
    private $sapIdAttr;

    private $ajaxHeaders;

    public function setUp(): void
    {
        parent::setUp();

        $this->titleAttr = App::isLocale('en')
            ? 'title'
            : trans('validation.attributes.title');

        $this->sapIdAttr = trans('validation.attributes.sap_id');

        $this->ajaxHeaders = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];
    }

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
            ->withHeaders($this->ajaxHeaders)
            ->post(
                '/accounts',
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
            ->withHeaders($this->ajaxHeaders)
            ->post(
                '/accounts',
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

    public function test_request_failure_when_title_too_long()
    {
        $user = User::create([ 'email' => 'new@b.c' ]);

        $response = $this->actingAs($user)
            ->withHeaders($this->ajaxHeaders)
            ->post(
                '/accounts',
                [
                    'title' => str_repeat('a', 256),
                    'sap_id' => '',
                ]
            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.title.0',
                trans('validation.max.string', [ 'attribute' => $this->titleAttr, 'max' => 255 ])
            );
    }

    public function test_request_failure_when_sap_id_too_long()
    {
        $user = User::create([ 'email' => 'new@b.c' ]);

        $response = $this->actingAs($user)
            ->withHeaders($this->ajaxHeaders)
            ->post(
                '/accounts',
                [
                    'title' => '',
                    'sap_id' => str_repeat('a', 256),
                ]
            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.sap_id.0',
                trans('validation.max.string', [ 'attribute' => $this->sapIdAttr, 'max' => 255 ])
            );
    }

    public function test_request_failure_when_sap_id_has_incorrect_format()
    {
        $user = User::create([ 'email' => 'new@b.c' ]);

        $response = $this->actingAs($user)
            ->withHeaders($this->ajaxHeaders)
            ->post(
                '/accounts',
                [
                    'title' => 'title',
                    'sap_id' => 'AO-06/0-',
                ]
            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.sap_id.0',
                trans('validation.regex', [ 'attribute' => $this->sapIdAttr ])
            );
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

        $response = $this->actingAs($user)->get('/');
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.index');

        $accounts = $response->viewData('accounts');

        $this->assertCount(2,$accounts);
        $this->assertEquals(10,$accounts[0]->getBalance());
        $this->assertEquals(-10,$accounts[1]->getBalance());
    }

}
