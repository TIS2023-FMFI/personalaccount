<?php

namespace App\Http\Controllers\SapReports;

use App\Exceptions\DatabaseException;
use App\Exceptions\StorageException;
use App\Http\Helpers\DBTransaction;
use \Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\SapReports\UploadReportRequest;
use App\Models\Account;
use App\Models\SapReport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * A controller responsible for uploading new SAP reports.
 * 
 * This controller provides methods to:
 *      - upload a SAP report
 */
class UploadReportController extends Controller
{
    /**
     * Handle a request to upload a SAP report.
     * 
     * @param \App\Models\Account $account
     * the account with which to associate the report
     * @param \App\Http\Requests\SapReports\UploadReportRequest $request
     * the request containing the SAP report file and the id of an account with
     * which to associate the report
     * @return \Illuminate\Http\Response
     * a response containing the information about the result of this operation
     * presented as a plain-text message
     */
    public function uploadReport(UploadReportRequest $request)
    {
        $account = Account::findOrFail($request->validated('account_id'));

        // $this->authorize('create', [SapReport::class, $account]);

        $report = $request->file('sap_report');

        try {
            $this->uploadReportFile($account, $report);
        } catch (Exception $e) {
            return response(trans('sap_reports.upload.failed'), 500);
        }

        return response(trans('sap_reports.upload.success'), 201);
    }

    /**
     * Upload a SAP report.
     * 
     * @param \App\Models\Account $account
     * the account with which to associate the report
     * @throws \Exception
     * thrown if an error occurred
     * @param \Illuminate\Http\UploadedFile $report
     * the SAP report file to upload
     * @return void
     */
    private function uploadReportFile(Account $account, UploadedFile $report)
    {
        $accountOwner = User::findOrFail($account->user_id);
        $reportPath = $this->saveReportFileToUserStorage($accountOwner, $report);

        $createRecordTransaction = new DBTransaction(
            fn() => $this->createReportRecord($account, $reportPath),
            fn() => Storage::delete($reportPath)
        );

        $createRecordTransaction->run();
    }

    /**
     * Save a SAP report to the storage reserved for a user.
     * 
     * @param \App\Models\Account $account
     * the user under which to save the report
     * @param \Illuminate\Http\UploadedFile $report
     * the SAP report file to upload
     * @return string
     * the path to the saved SAP report file
     */
    private function saveReportFileToUserStorage(User $user, UploadedFile $report)
    {
        $reportsDirectoryPath = SapReport::getReportsDirectoryPath($user);
        $reportPath = Storage::putFile($reportsDirectoryPath, $report);
    
        if (!$reportPath) {
            throw new StorageException('File not saved.');
        }

        return $reportPath;
    }

    /**
     * Create and persist a SAP Report model representing the saved SAP report
     * file.
     * 
     * @param Account $account
     * the account with which to associate the report
     * @param string $reportPath
     * the path to the saved SAP report file
     * @throws DatabaseException
     * thrown if the SAP Report model could not be persisted
     * @return void
     */
    private function createReportRecord(Account $account, string $reportPath)
    {
        $report = SapReport::create([
            'account_id' => $account,
            'path' => $reportPath,
            'uploaded_on' => now(),
        ]);

        if (!$report->exists) {
            throw new DatabaseException('Record not saved.');
        }
    }
}
