<?php

namespace App\Http\Controllers\SapReports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Base\DateRequest;
use App\Models\Account;
use Illuminate\Support\Carbon;

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
     * the date they were exported or uploaded. The filtered reports are paginated.
     *
     * @param \App\Http\Requests\Base\DateRequest $request
     * the request containing the date interval used for filtering
     * @param \App\Models\Account $account
     * the account for which to show the SAP reports
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * the view that will be shown
     */
    public function show(DateRequest $request, Account $account)
    {
        $from = $request->getValidatedFromDateOrMin();
        $to = $request->getValidatedToDateOrMax();
        $reports = $this->retrieveSapReports($account, $from, $to);
        $accountTitle = $account->user->first()->pivot->account_title;
        return view('finances.sap_reports', [
            'account' => $account,
            'account_title' => $accountTitle,
            'reports' => $reports
        ]);
    }

    /**
     * Retrieve the paginated SAP Reports for an account which were exported or
     * uploaded within a specified period.
     *
     * @param \App\Models\Account $account
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
                ->orderBy('exported_or_uploaded_on', 'desc')
                ->paginate($this::$resultsPerPage)
                ->withQueryString();
    }
}
