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

class UpdateOperationTest extends TestCase
{
    use DatabaseTransactions;

    private Model $user, $account, $type, $lendingType;
    private array $headers;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(['email' => 'a@b.c']);
        $this->account = Account::factory()->create(['user_id' => $this->user]);
        $this->type = OperationType::factory()->create(['name' => 'test']);
        $this->lendingType = OperationType::factory()->create(['name' => 'Lending']);

        $this->headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];

    }

    public function test_edit_view(){

        $operation = FinancialOperation::factory()->create(['account_id' => $this->account, 'operation_type_id' => $this->type]);

        $response = $this->actingAs($this->user)->get("/edit_operation/$operation->id");
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.modals.edit_operation');
    }

    public function test_edit_view_data(){

        $operation = FinancialOperation::factory()->create(['account_id' => $this->account, 'operation_type_id' => $this->type]);

        $response = $this->actingAs($this->user)->get("/edit_operation/$operation->id");
        $this->assertTrue($operation->is($response->viewData('operation')));
        $this->assertEquals(null,$operation->is($response->viewData('lending')));

    }

    public function test_update_operation(){

        $operation = FinancialOperation::factory()->create(
            [
                'account_id' => $this->account,
                'operation_type_id' => $this->type,
                'title' => 'original',
                'sum' => 100,
                'subject' => 'original'
            ]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->post('/edit_operation', ['id' => $operation->id, 'sum' => 100.5, 'subject' => 'new']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('financial_operations', [
            'id' => $operation->id,
            'title' => 'original',
            'sum' => 100.5,
            'subject' => 'new'
        ]);
    }

    public function test_update_nonexisting_operation(){

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->post('/edit_operation', ['id' => 99999, 'sum' => 100.5, 'subject' => 'new']);

        $response->assertStatus(422);
    }

    public function test_update_type_to_lending_creates_lending_record(){

        $operation = FinancialOperation::factory()->create(
            ['account_id' => $this->account, 'operation_type_id' => $this->type]);

        $this->assertDatabaseMissing('lendings', ['id' => $operation->id]);

        $response = $this->actingAs($this->user)
            ->withHeaders($this->headers)->post('/edit_operation', [
                'id' => $operation->id,
                'operation_type_id' => $this->lendingType->id,
                'expected_date_of_return' => '2023-01-01'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('lendings', [
            'id' => $operation->id,
            'expected_date_of_return' => '2023-01-01'
        ]);
    }

    public function test_update_type_from_lending_deletes_lending_record(){

        $operation = FinancialOperation::factory()->create(
            ['account_id' => $this->account, 'operation_type_id' => $this->lendingType]);
        Lending::factory()->create(['id' => $operation]);

        $this->assertDatabaseHas('lendings', ['id' => $operation->id]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->post('/edit_operation', [
                'id' => $operation->id,
                'operation_type_id' => $this->type->id
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('lendings', ['id' => $operation->id]);
    }

    public function test_update_operation_replaces_file(){

        Storage::fake('local');

        $dir = sprintf('user_%d/attachments', $this->user->id);
        $file = UploadedFile::fake()->create('test.txt');
        $name = 'attachment_0000';
        $oldPath = sprintf('%s/%s', $dir, $name);

        Storage::putFileAs($dir, $file, $name);
        Storage::assertExists($oldPath);

        $operation = FinancialOperation::factory()->create(
            ['account_id' => $this->account, 'operation_type_id' => $this->type, 'attachment' => $oldPath]);

        $response = $this->actingAs($this->user)->withHeaders($this->headers)
            ->post('/edit_operation', [
                'id' => $operation->id,
                'attachment' => $file
            ]);

        $response->assertStatus(200);
        $operation->refresh();
        $newPath = $operation->attachment;
        Storage::disk('local')->assertExists($newPath);
        Storage::disk('local')->assertMissing($oldPath);

        Storage::fake('local');

    }

}
