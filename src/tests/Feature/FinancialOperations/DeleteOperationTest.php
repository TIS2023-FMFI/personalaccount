<?php

namespace Tests\Feature\FinancialOperations;

use App\Http\Controllers\FinancialOperations\OperationsOverviewController;
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

/**
 * These tests must be run on a seeded database, as they generate plenty of models with foreign keys.
 */
class DeleteOperationTest extends TestCase
{
    use DatabaseTransactions;

    private Model $user, $account, $type, $lendingType;
    private array $headers;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate([ 'email' => 'new@b.c' ]);
        $this->account = Account::factory()->create(['user_id' => $this->user]);
        $this->type = OperationType::firstOrCreate(['name' => 'type']);
        $this->lendingType = OperationType::firstOrCreate(['name' => 'lending', 'lending' => true]);

        $this->headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];
    }

    public function test_delete_operation()
    {

        $operation = FinancialOperation::factory()
            ->create(['account_id' => $this->account, 'operation_type_id' => $this->type]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->delete("/operations/$operation->id");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('financial_operations', [
            'id' => $operation->id,
        ]);
    }

    public function test_delete_operation_with_lending()
    {

        $operation = FinancialOperation::factory()
            ->create(['account_id' => $this->account, 'operation_type_id' => $this->lendingType]);
        Lending::factory()->create(['id' => $operation]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->delete("/operations/$operation->id");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('financial_operations', [
            'id' => $operation->id,
        ]);
        $this->assertDatabaseMissing('lendings', [
            'id' => $operation->id,
        ]);
    }

    public function test_delete_operation_with_file()
    {

        Storage::fake('local');
        $file = UploadedFile::fake()->create('test.pdf')->store('local');
        Storage::disk('local')->assertExists($file);

        $operation = FinancialOperation::factory()->create(
            ['account_id' => $this->account, 'attachment' => $file, 'operation_type_id' => $this->type]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->delete("/operations/$operation->id");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('financial_operations', [
            'id' => $operation->id,
        ]);
        Storage::disk('local')->assertMissing($file);

    }

}
