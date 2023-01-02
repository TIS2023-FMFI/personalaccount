<?php

namespace Tests\Feature\Operations;

use App\Http\Controllers\FinancialAccounts\GeneralOperationController;
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

class OperationDetailTest extends TestCase
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
        $this->type = OperationType::factory()->create();
        $this->lendingType = OperationType::factory()->create(['name' => 'Lending']);

        $this->headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];

        $this->controller = new GeneralOperationController;

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

    public function test_cant_view_nonexistent_operation(){

        $response = $this->actingAs($this->user)->get("/operation/99999");
        $response->assertStatus(404);
    }

    public function test_attachment_download(){

        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.txt');
        $path = $this->controller->saveAttachment($this->user->id, $file);

        Storage::assertExists($path);

        $operation = FinancialOperation::factory()
            ->create([
                'account_id' => $this->account,
                'operation_type_id' => $this->type,
                'attachment' => $path
                ]);

        $response = $this->actingAs($this->user)->get(sprintf('/attachment/%d', $operation->id));

        $response->assertStatus(200);
        $response->assertDownload();

        Storage::fake('local');

    }

    public function test_cant_download_nonexistent_attachment(){

        Storage::fake('local');

        $operation = FinancialOperation::factory()
            ->create([
                'account_id' => $this->account,
                'operation_type_id' => $this->type,
                'attachment' => ''
            ]);

        $response = $this->actingAs($this->user)->get(sprintf('/attachment/%d', $operation->id));

        $response->assertStatus(500);
    }

}
