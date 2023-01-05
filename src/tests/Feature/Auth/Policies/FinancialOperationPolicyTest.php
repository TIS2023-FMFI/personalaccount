<?php

namespace Tests\Feature\Auth\Policies;

use App\Http\Controllers\FinancialOperations\GeneralOperationController;
use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\OperationType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinancialOperationPolicyTest extends TestCase
{
    private $user;
    private $otherUser;
    private $account;
    private $otherAccount;
    private $operation;

    private $ajaxHeaders;

    private $setupDone = false;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->setupDone) {
            return;
        }

        $this->user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
        $this->account = Account::factory()->create([ 'user_id' => $this->user ]);
        
        $this->otherUser = User::firstOrCreate([ 'email' => 'new@b.c' ]);
        $this->otherAccount = Account::factory()->create([ 'user_id' => $this->otherUser ]);

        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.txt');
        $path = (new GeneralOperationController())
                    ->saveAttachment($this->user->id, $file);

        Storage::assertExists($path);

        $type = OperationType::firstOrCreate([ 'name' => 'type' ]);
        $this->operation = FinancialOperation::factory()
                            ->create([
                                'title' => 'operation',
                                'account_id' => $this->account,
                                'operation_type_id' => $type,
                                'attachment' => $path
                            ]);

        $this->ajaxHeaders = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];

        $this->setupDone = true;
    }

    public function test_that_unauthorized_user_cannot_view_operation()
    {
        $response = $this->actingAs($this->otherUser)
                            ->get("/operation/" . $this->operation->id);
        
        $response
            ->assertStatus(403);
    }

    public function test_that_unauthorized_user_cannot_download_attachement()
    {
        $response = $this->actingAs($this->otherUser)
                            ->get("/attachment/" . $this->operation->id);
        
        $response
            ->assertStatus(403);
    }

    public function test_that_unauthorized_user_cannot_create_operation()
    {
        $newOperation = $this->operation->getAttributes();
        $newOperation['title'] = 'new title';
        unset($newOperation['attachment']);

        $response = $this->actingAs($this->otherUser)
                            ->withHeaders($this->ajaxHeaders)
                            ->post("/operation", $newOperation);
        
        $response
            ->assertStatus(403);
    }
    
    public function test_that_unauthorized_user_cannot_update_operation()
    {
        $updated = $this->operation->getAttributes();
        $updated['title'] = 'new title';
        unset($updated['attachment']);

        $response = $this->actingAs($this->otherUser)
                            ->withHeaders($this->ajaxHeaders)
                            ->put(
                                "/operation/" . $this->operation->id,
                                $updated
                            );
        
        $response
            ->assertStatus(403);
    }

    public function test_that_unauthorized_user_cannot_move_operation()
    {
        $updated = $this->operation->getAttributes();
        $updated['account_id'] = $this->otherAccount->id;
        unset($updated['attachment']);

        $response = $this->actingAs($this->user)
                            ->withHeaders($this->ajaxHeaders)
                            ->put(
                                "/operation/" . $this->operation->id,
                                $updated
                            );
        
        $response
            ->assertStatus(403);
    }

    public function test_that_unauthorized_user_cannot_change_checked_state_of_operation()
    {
        $response = $this->actingAs($this->otherUser)
                            ->withHeaders($this->ajaxHeaders)
                            ->patch("/operation/" . $this->operation->id);
        
        $response
            ->assertStatus(403);
    }

    public function test_that_unauthorized_user_cannot_delete_operation()
    {
        $response = $this->actingAs($this->otherUser)
                            ->withHeaders($this->ajaxHeaders)
                            ->delete("/operation/" . $this->operation->id);
        
        $response
            ->assertStatus(403);
    }
}
