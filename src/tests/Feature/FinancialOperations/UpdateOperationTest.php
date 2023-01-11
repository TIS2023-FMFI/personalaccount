<?php

namespace Tests\Feature\FinancialOperations;

use App\Http\Controllers\FinancialOperations\GeneralOperationController;
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

class UpdateOperationTest extends TestCase
{
    use DatabaseTransactions;

    private Model $user, $account, $type, $lendingType;
    private array $headers;
    private GeneralOperationController $controller;

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

        $this->controller = new GeneralOperationController;

    }

    public function test_update_operation(){

        $operation = FinancialOperation::factory()->create(
            [
                'account_id' => $this->account,
                'operation_type_id' => $this->type
            ]);

        $operationData = [
            'title' => 'title',
            'date' => '2022-12-24',
            'operation_type_id' => $this->type->id,
            'subject' => 'subject',
            'sum' => 100,
            'attachment' => null
        ];

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->put("/operations/$operation->id", $operationData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('financial_operations', [
            'id' => $operation->id,
            'title' => 'title',
            'sum' => 100,
            'subject' => 'subject'
        ]);
    }

    public function test_update_nonexisting_operation(){

        $operationData = [
            'title' => 'title',
            'date' => '2022-12-24',
            'operation_type_id' => $this->type->id,
            'subject' => 'subject',
            'sum' => 100,
            'attachment' => null
        ];

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->put('/operations/9999', $operationData);

        $response->assertStatus(404);
    }

    public function test_update_type_to_lending_creates_lending_record(){

        $operation = FinancialOperation::factory()->create(
            [
                'account_id' => $this->account,
                'operation_type_id' => $this->type
            ]);

        $this->assertDatabaseMissing('lendings', ['id' => $operation->id]);

        $operationData = [
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
            ->put("/operations/$operation->id", array_merge($operationData, $lendingData));

        $response->assertStatus(200);
        $this->assertDatabaseHas('lendings', [
            'id' => $operation->id,
            'expected_date_of_return' => '2023-01-01'
        ]);
    }

    public function test_update_type_from_lending_deletes_lending_record(){

        $operation = FinancialOperation::factory()->create(
            [
                'account_id' => $this->account,
                'operation_type_id' => $this->lendingType
            ]);
        Lending::factory()->create(['id' => $operation]);

        $this->assertDatabaseHas('lendings', ['id' => $operation->id]);

        $operationData = [
            'title' => 'test',
            'date' => '2022-12-24',
            'operation_type_id' => $this->type->id,
            'subject' => 'test',
            'sum' => 100,
            'attachment' => null
        ];

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->put("/operations/$operation->id", $operationData);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('lendings', ['id' => $operation->id]);
    }

    public function test_update_operation_creates_file(){

        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.txt');

        $operation = FinancialOperation::factory()->create(
            [
                'account_id' => $this->account,
                'operation_type_id' => $this->type,
                'attachment' => ''
            ]);

        $operationData = [
            'title' => 'test',
            'date' => '2022-12-24',
            'operation_type_id' => $this->type->id,
            'subject' => 'test',
            'sum' => 100,
            'attachment' => $file
        ];

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->put("/operations/$operation->id", $operationData);

        $response->assertStatus(200);
        $operation->refresh();
        $path = $operation->attachment;
        Storage::disk('local')->assertExists($path);

        Storage::fake('local');

    }

    public function test_update_operation_replaces_file(){

        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.txt');
        $oldPath = $this->controller->saveAttachment($this->user, $file);

        Storage::assertExists($oldPath);

        $operation = FinancialOperation::factory()->create(
            [
                'account_id' => $this->account,
                'operation_type_id' => $this->type,
                'attachment' => $oldPath
            ]);

        $newFile = UploadedFile::fake()->create('test.txt');
        $operationData = [
            'title' => 'test',
            'date' => '2022-12-24',
            'operation_type_id' => $this->type->id,
            'subject' => 'test',
            'sum' => 100,
            'attachment' => $newFile
        ];

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->put("/operations/$operation->id", $operationData);

        $response->assertStatus(200);
        $operation->refresh();
        $newPath = $operation->attachment;
        Storage::disk('local')->assertExists($newPath);
        Storage::disk('local')->assertMissing($oldPath);
    }

    public function test_check_operation()
    {

        $operation = FinancialOperation::factory()->create(
            ['account_id' => $this->account, 'checked' => false, 'operation_type_id' => $this->type]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->patch("/operations/$operation->id", ['checked' => true]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('financial_operations', [
            'id' => $operation->id,
            'checked' => true
        ]);
    }

    public function test_uncheck_operation()
    {

        $operation = FinancialOperation::factory()->create(
            ['account_id' => $this->account, 'checked' => true, 'operation_type_id' => $this->type]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->patch("/operations/$operation->id", ['checked' => false]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('financial_operations', [
            'id' => $operation->id,
            'checked' => false
        ]);
    }

    public function test_cannnot_check_lending()
    {

        $operation = FinancialOperation::factory()->create(
            ['account_id' => $this->account, 'checked' => false, 'operation_type_id' => $this->lendingType]);
        Lending::factory()->create(['id' => $operation]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->patch("/operations/$operation->id", ['checked' => true]);

        $response->assertStatus(422);

    }

}
