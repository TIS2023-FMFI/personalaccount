<?php

namespace App\Http\Controllers\SapReports;

use App\Http\Controllers\Controller;
use App\Http\Requests\SapReports\ShowReportsRequest;
use App\Models\Account;
use App\Models\SapReport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * A controller responsible for presenting existing SAP reports to a user.
 * 
 * This controller provides methods to:
 *      - show a list of SAP reports associated with an account
 */
class ReportsOverviewController extends Controller
{
    /**
     * The number of SAP reports to show on a single page.
     * 
     * @var int
     */
    private static int $resultsPerPage = 15;

    /**
     * Show the SAP Reports view for an account with reports filtered based on
     * the date they were uploaded. The filtered reports are paginated.
     * 
     * @param ShowReportsRequest $request
     * the request containing the date interval used for filtering
     * @param Account $account
     * the account for which to show the SAP reports
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * the view that will be shown
     */
    public function show(ShowReportsRequest $request, Account $account)
    {
        $this->authorize('view', $account);
        
        $from = $request->getValidatedFromDateOrMin();
        $to = $request->getValidatedToDateOrMin();
        $reports = $this->retrieveSapReports($account, $from, $to);

        return view('finances.sap_reports', [
            'reports' => $reports,
        ]);
    }

    /**
     * Retrieve the paginated SAP Reports for an account which were uploaded
     * within a specified period.
     * 
     * @param Account $account
     * the account for which to show the SAP reports
     * @param \Illuminate\Support\Carbon $from
     * the date determining the beginning of the period to consider (inclusive)
     * @param \Illuminate\Support\Carbon $to
     * the date determining the end of the period to consider (inclusive)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * the paginated and filtered reports
     */
    private function retrieveSapReports(Account $account, Carbon $from, Carbon $to)
    {
        return $account
                ->sapReportsBetween($from, $to)
                ->orderBy('upladed_on', 'desc')
                ->paginate($this::$resultsPerPage)
                ->withQueryString();
    }
}
