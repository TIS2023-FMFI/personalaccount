<?php

namespace Tests\Feature\Operations;

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
        $this->type = OperationType::factory()->create(['name' => 'type', 'lending' => false]);
        $this->lendingType = OperationType::factory()->create(['name' => 'lending', 'lending' => true]);

        $this->headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];

        $this->controller = new GeneralOperationController;

    }

    public function test_operation_data(){

        $operation = FinancialOperation::factory()->create(['account_id' => $this->account, 'operation_type_id' => $this->type]);

        $response = $this->actingAs($this->user)->get("/operation/$operation->id");
        $content = $response->json();

        $this->assertEquals($this->account->id, $content['operation']['account_id']);
        $this->assertEquals($this->type->id, $content['operation']['operation_type_id']);
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
                'title' => 'operation',
                'account_id' => $this->account,
                'operation_type_id' => $this->type,
                'attachment' => $path
                ]);

        $response = $this->actingAs($this->user)->get("/attachment/$operation->id");

        $response->assertStatus(200);
        $response->assertDownload();
        $this->assertEquals('attachment; filename=attachment_operation.', $response->headers->get('content-disposition'));

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

        $response = $this->actingAs($this->user)->get("/attachment/$operation->id");

        $response->assertStatus(500);
    }

}
