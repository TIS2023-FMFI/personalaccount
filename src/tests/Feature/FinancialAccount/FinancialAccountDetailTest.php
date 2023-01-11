<?php

namespace Tests\Feature\FinancialAccount;

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
class FinancialAccountDetailTest extends TestCase
{
    use DatabaseTransactions;

    private int $perPage;
    private Model $user, $account, $type, $lendingType;
    private array $headers;

    public function setUp(): void
    {
        parent::setUp();

        $this->perPage = OperationsOverviewController::$resultsPerPage;
        $this->user = User::firstOrCreate([ 'email' => 'new@b.c' ]);
        $this->account = Account::factory()->create(['user_id' => $this->user]);
        $this->type = OperationType::firstOrCreate(['name' => 'type']);
        $this->lendingType = OperationType::firstOrCreate(['name' => 'lending', 'lending' => true]);

        $this->headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];
    }

    public function test_correct_view()
    {
        $account = Account::factory()->create(['user_id' => $this->user]);
        $response = $this->actingAs($this->user)->get("/accounts/$account->id/operations");
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.account');
    }

    public function test_correct_view_data()
    {

        $account = Account::factory()->has(FinancialOperation::factory()->count(5))
            ->create(['user_id' => $this->user]);

        $response = $this->actingAs($this->user)->get("/accounts/$account->id/operations");
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.account');

        $data = $response->viewData('operations');

        $this->assertCount(5,$data);
    }

    public function test_pagination_is_used()
    {

        $count = $this->perPage;
        $account = Account::factory()->has(FinancialOperation::factory()->count($count + 1))
            ->create(['user_id' => $this->user]);

        $response = $this->actingAs($this->user)->get("/accounts/$account->id/operations");
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.account');

        $operations = $response->viewData('operations');

        $this->assertCount($count,$operations);

        $this->assertTrue($operations->hasPages());
        $this->assertEquals($count+1,$operations->total());
        $this->assertEquals(2,$operations->lastPage());

    }

    public function test_paging_second_page()
    {

        $count = $this->perPage;
        $account = Account::factory()->has(FinancialOperation::factory()->count($count + 1))
            ->create(['user_id' => $this->user]);

        $response = $this->actingAs($this->user)->get("/accounts/$account->id/operations?page=2");
        $response
            ->assertStatus(200)
            ->assertViewIs('finances.account');

        $operations = $response->viewData('operations');

        $this->assertCount(1,$operations);
        $this->assertFalse($operations->hasMorePages());
    }

}
