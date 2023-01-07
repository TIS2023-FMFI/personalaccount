<?php

namespace Tests\Feature\SapReports;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportDetalTest extends TestCase
{
    private $user, $account;
    private $accountIdAttr, $sapReportAttr;

    private $ajaxHeaders;

    private $setupDone = false;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->setupDone) {
            return;
        }

        $this->accountIdAttr = 'account id';
        $this->sapReportAttr = trans('validation.attributes.sap_report');

        $this->user = User::firstOrCreate([ 'email' => 'a@b.c' ]);
        $this->account = Account::factory()->for($this->user)->create();

        $this->ajaxHeaders = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ];

        $this->setupDone = true;
    }

    public function test_that_unauthenticated_user_cannot_upload_report()
    {
        $response = $this->post('/sap-reports', [ 'account_id' => '', 'sap_report' => '' ]);

        $response
            ->assertStatus(302);
    }

    public function test_that_only_ajax_requests_are_handled()
    {
        $response = $this->actingAs($this->user)
                            ->post(
                                '/sap-reports',
                                [ 'account_id' => '', 'sap_report' => '' ]
                            );

        $response
            ->assertStatus(500);
    }

    public function test_that_account_id_is_required()
    {
        $response = $this->actingAs($this->user)
                            ->withHeaders($this->ajaxHeaders)
                            ->post(
                                '/sap-reports',
                                [ 'account_id' => '', 'sap_report' => '' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.account_id.0',
                trans('validation.required', [ 'attribute' => $this->accountIdAttr ])
            );
    }

    public function test_that_account_id_must_be_numeric()
    {
        $response = $this->actingAs($this->user)
                            ->withHeaders($this->ajaxHeaders)
                            ->post(
                                '/sap-reports',
                                [ 'account_id' => 'abc', 'sap_report' => '' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.account_id.0',
                trans('validation.numeric', [ 'attribute' => $this->accountIdAttr ])
            );
    }

    public function test_that_account_id_must_reference_an_existing_account()
    {
        $response = $this->actingAs($this->user)
                            ->withHeaders($this->ajaxHeaders)
                            ->post(
                                '/sap-reports',
                                [ 'account_id' => '99999', 'sap_report' => '' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.account_id.0',
                trans('validation.exists', [ 'attribute' => $this->accountIdAttr ])
            );
    }

    public function test_that_sap_report_is_required()
    {
        $response = $this->actingAs($this->user)
                            ->withHeaders($this->ajaxHeaders)
                            ->post(
                                '/sap-reports',
                                [ 'account_id' => '', 'sap_report' => '' ]
                            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.sap_report.0',
                trans('validation.required', [ 'attribute' => $this->sapReportAttr ])
            );
    }

    public function test_that_sap_report_must_be_text_file()
    {
        Storage::fake();
        $uploadedReport = UploadedFile::fake()
                            ->create('test', 0, 'application/pdf');

        $requestData = [
            'account_id' => $this->account->id,
            'sap_report' => $uploadedReport,
        ];
        
        $response = $this->actingAs($this->user)
                            ->withHeaders($this->ajaxHeaders)
                            ->post('/sap-reports', $requestData);

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.sap_report.0',
                trans('validation.mimes', [ 'attribute' => $this->sapReportAttr, 'values' => 'txt'])
            );
    }

    public function test_that_sap_report_is_uploaded_if_request_is_valid()
    {
        Storage::fake();
        $uploadedReport = UploadedFile::fake()
                            ->create('test', 0, 'text/plain');

        $requestData = [
            'account_id' => $this->account->id,
            'sap_report' => $uploadedReport,
        ];
        
        $response = $this->actingAs($this->user)
                            ->withHeaders($this->ajaxHeaders)
                            ->post('/sap-reports', $requestData);

        $response
            ->assertStatus(201);

        $report = $this->account->sapReports()->first();
        $this->assertNotNull($report);

        Storage::assertExists($report->path);

        $report->delete();
    }
}
