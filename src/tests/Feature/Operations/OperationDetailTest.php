<?php

namespace Tests\Feature\Operations;

use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\Lending;
use App\Models\OperationType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OperationDetailTest extends TestCase
{
    use DatabaseTransactions;

    private Model $user, $account, $type, $lendingType;
    private array $headers;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(['email' => 'a@b.c']);
        $this->account = Account::factory()->create(['user_id' => $this->user]);
        $this->type = OperationType::factory()->create();
        $this->lendingType = OperationType::factory()->create(['name' => 'Lending']);

        $this->headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];

    }

    public function test_operation_view(){

        $operation = FinancialOperation::factory()->create(['account_id' => $this->account, 'operation_type_id' => $this->type]);

        $response = $this->actingAs($this->user)->get("/operation/$operation->id");
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.modals.operation');
    }

    public function test_operation_view_data(){

        $operation = FinancialOperation::factory()->create(['account_id' => $this->account, 'operation_type_id' => $this->type]);

        $response = $this->actingAs($this->user)->get("/operation/$operation->id");
        $this->assertTrue($operation->is($response->viewData('operation')));
        $this->assertEquals(null,$operation->is($response->viewData('lending')));

    }

    public function test_operation_view_data_with_lending(){

        $operation = FinancialOperation::factory()->create(['account_id' => $this->account, 'operation_type_id' => $this->lendingType]);
        $lending = Lending::factory()->create(['id' => $operation]);

        $response = $this->actingAs($this->user)->get("/operation/$operation->id");
        $this->assertTrue($operation->is($response->viewData('operation')));
        $this->assertTrue($lending->is($response->viewData('lending')));

    }

}
