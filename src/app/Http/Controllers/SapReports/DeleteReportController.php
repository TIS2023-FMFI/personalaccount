<?php

namespace App\Http\Controllers\SapReports;

use App\Exceptions\DatabaseException;
use App\Exceptions\StorageException;
use App\Http\Controllers\Controller;
use App\Http\Helpers\DBTransaction;
use App\Models\SapReport;
use \Exception;
use Illuminate\Support\Facades\Storage;

/**
 * Summary of DeleteReportController
 */
class DeleteReportController extends Controller
{
    /**
     * Handle a request to delete a SAP report.
     * 
     * @param \App\Models\SapReport $report
     * the SAP report to delete
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * a response containing the information about the result of this operation
     * presented as a plain-text message
     */
    public function delete(SapReport $report)
    {
        // $this->authorize('delete', $report);

        try {
            $this->deleteWithinTransaction($report);
        } catch (Exception $e) {
            return response(trans('sap_reports.delete.failed'), 500);
        }

        return response(trans('sap_reports.delete.success'), 200);
    }

    /**
     * Delete a SAP report within a database transaction.
     * 
     * @param SapReport $report
     * the SAP report to delete
     * @throws \Exception
     * thrown if an error occurred
     * @return void
     */
    private function deleteWithinTransaction(SapReport $report)
    {
        $deleteRecordAndFileTransaction = new DBTransaction(
            fn() => $this->deleteReportRecordAndFile($report)
        );

        $deleteRecordAndFileTransaction->run();
    }

    /**
     * Delete a SAP report record and then the associated file. The deletions are
     * performed in this order, so that a call to this method can be wrapped in
     * a database transaction.
     * 
     * @param SapReport $report
     * the SAP report to delete
     * @throws DatabaseException
     * thrown if the SAP Report model could not be deleted
     * @throws StorageException
     * thrown if the SAP report file could not be deleted
     * @return void
     */
    private function deleteReportRecordAndFile(SapReport $report)
    {
        $path = $report->path;
        
        if (!$report->delete()) {
            throw new DatabaseException('Record not deleted.');
        }

        if (!Storage::delete($path)) {
            throw new StorageException('File not deleted.');
        }
    }
}
