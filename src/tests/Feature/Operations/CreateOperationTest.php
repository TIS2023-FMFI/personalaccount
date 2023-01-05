<?php

namespace Tests\Feature\Operations;

use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\Lending;
use App\Models\OperationType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateOperationTest extends TestCase
{
    use DatabaseTransactions;

    private Model $user, $account, $type, $lendingType;
    private array $headers;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(['email' => 'a@b.c']);
        $this->account = Account::factory()->create(['user_id' => $this->user]);
        $this->type = OperationType::firstOrCreate(['name' => 'type', 'lending' => false]);
        $this->lendingType = OperationType::firstOrCreate(['name' => 'lending', 'lending' => true]);

        $this->headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];

    }

    public function test_create_operation(){

        $operationData = [
            'account_id' => $this->account->id,
            'title' => 'test',
            'date' => '2022-12-24',
            'operation_type_id' => $this->type->id,
            'subject' => 'test',
            'sum' => 100,
            'attachment' => null
        ];

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->post('/operation', $operationData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('financial_operations', $operationData);

    }

    public function test_create_operation_invalid_sum(){

        $operationData = [
            'account_id' => $this->account->id,
            'title' => 'test',
            'date' => '2022-12-24',
            'operation_type_id' => $this->type->id,
            'subject' => 'test',
            'sum' => -100,
            'attachment' => null
        ];

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->post('/operation', $operationData);

        $response->assertStatus(422);

    }

    public function test_create_operation_with_lending(){

        $operationData = [
            'account_id' => $this->account->id,
            'title' => 'test',
            'date' => '2022-12-24',
            'operation_type_id' => $this->lendingType->id,
            'subject' => 'test',
            'sum' => 100,
            'attachment' => null
        ];

        $lendingData = [
            'expected_date_of_return' => '2023-01-01',
            'previous_lending_id' => null
        ];

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->post('/operation', array_merge($operationData, $lendingData));

        $response->assertStatus(201);
        $this->assertDatabaseHas('financial_operations', $operationData);
        $this->assertDatabaseHas('lendings', $lendingData);

    }

    public function test_create_operation_with_lending_referencing_previous(){

        $previousLendingOperation = FinancialOperation::factory()->create(['account_id' => $this->account, 'operation_type_id' => $this->lendingType]);
        $previousLending = Lending::factory()->create(['id' => $previousLendingOperation]);

        $operationData = [
            'account_id' => $this->account->id,
            'title' => 'test',
            'date' => '2022-12-24',
            'operation_type_id' => $this->lendingType->id,
            'subject' => 'test',
            'sum' => 100,
            'attachment' => null
        ];

        $lendingData = [
            'expected_date_of_return' => '2023-01-01',
            'previous_lending_id' => $previousLending->id
        ];

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->post('/operation', array_merge($operationData, $lendingData));

        $response->assertStatus(201);
        $this->assertDatabaseHas('financial_operations', $operationData);
        $this->assertDatabaseHas('lendings', $lendingData);

    }

    public function test_create_operation_with_lending_cannot_reference_nonlending(){

        $previousOperation = FinancialOperation::factory()->create(['account_id' => $this->account, 'operation_type_id' => $this->type]);

        $operationData = [
            'account_id' => $this->account->id,
            'title' => 'test',
            'date' => '2022-12-24',
            'operation_type_id' => $this->lendingType->id,
            'subject' => 'test',
            'sum' => 100,
            'attachment' => null
        ];

        $lendingData = [
            'expected_date_of_return' => '2023-01-01',
            'previous_lending_id' => $previousOperation->id
        ];

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->post('/operation', array_merge($operationData, $lendingData));

        $response->assertStatus(422);

    }

    public function test_create_operation_with_file(){

        Storage::fake('local');

        $operationData = [
            'account_id' => $this->account->id,
            'title' => 'test_with_file',
            'date' => '2022-12-24',
            'operation_type_id' => $this->type->id,
            'subject' => 'test',
            'sum' => 100,
            'attachment' => UploadedFile::fake()->create('test.pdf')
        ];

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->post('/operation', $operationData);

        $response->assertStatus(201);
        $path = FinancialOperation::firstWhere('title', 'test_with_file')->attachment;
        Storage::disk('local')->assertExists($path);

        Storage::fake('local');

    }

}
