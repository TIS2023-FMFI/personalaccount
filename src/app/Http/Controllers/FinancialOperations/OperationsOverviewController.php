<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Base\DateRequest;
use App\Models\Account;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Manages the 'operations overview' view, as well as listing, filtering and exporting operations.
 */
class OperationsOverviewController extends Controller
{
    /**
     * @var int
     * number of operations to be shown on one page
     */
    public static int $perPage = 15;

    /**
     * shortcut for the $perPage static variable
     *
     * @return int
     */
    private function getPerPage()
    {
        return OperationsOverviewController::$perPage;
    }

    /**
     * Fills the 'operations overview' view with financial operations belonging to a financial account.
     * The operations are paginated and can be filtered by date.
     *
     * @param Account $account
     * the financial account to which the operations belong
     * @param DateRequest $request
     * a HTTP request which may contain the dates to filter the operations by
     * @return Application|Factory|View
     * the view filled with data
     */
    public function show(Account $account, DateRequest $request)
    {
        $dateFrom = $this->getFromDateOrMin($request);
        $dateTo = $this->getToDateOrMax($request);

        $incomes = $account->operationsBetween($dateFrom, $dateTo)->incomes()->sum('sum');
        $expenses = $account->operationsBetween($dateFrom, $dateTo)->expenses()->sum('sum');
        $operations = $account->operationsBetween($dateFrom, $dateTo)->orderBy('date', 'desc')
                              ->paginate($this->getPerPage())->withQueryString();

        return view('finances.account', [
            'account' => $account,
            'operations' => $operations,
            'incomes_total' => $incomes,
            'expenses_total' => $expenses
        ]);
    }

    /**
     * If the operations are requested to be filtered by date, gets the earliest date of the requested interval
     * (all operations before this date should be filtered out).
     * If no such date is present, gets the minimal possible date instead.
     *
     * @param DateRequest $request
     * a HTTP request which may contain the dates to filter the operations by
     * @return Carbon
     * the obtained date
     */
    private function getFromDateOrMin(DateRequest $request)
    {
        $date = $request->validated('from');
        if ($date) return Date::create($date);
        return Date::minValue();
    }

    /**
     * If the operations are requested to be filtered by date, gets the latest date of the requested interval
     * (all operations after this date should be filtered out).
     * If no such date is present, gets the maximal possible date instead.
     *
     * @param DateRequest $request
     * a HTTP request which may contain the dates to filter the operations by
     * @return Carbon
     * the obtained date
     */
    private function getToDateOrMax(DateRequest $request)
    {
        $date = $request->validated('to');
        if ($date) return Date::create($date);
        return Date::maxValue();
    }

    /**
     * Handles a request to download a CSV export of financial operations.
     *
     * @param Account $account
     * the financial account to which the operations belong
     * @param DateRequest $request
     * a HTTP request which may contain the dates to filter the operations by
     * @return StreamedResponse
     * a response allowing the user to download the CSV file
     */
    public function downloadExport(Account $account, DateRequest $request)
    {
        $dateFrom = $this->getFromDateOrMin($request);
        $dateTo = $this->getToDateOrMax($request);

        $operations = $account->operationsBetween($dateFrom, $dateTo)->orderBy('date', 'desc')->get();
        $filename = $this->generateExportName($account, $dateFrom, $dateTo);

        return response()->streamDownload(function() use ($operations){ $this->generateCSVfile($operations); }, $filename);
    }

    /**
     * Generates a name for the CSV export file, containing the name of the account.
     * If the dates in the export are limited, the bounding dates are present in the name as well.
     *
     * @param $account
     * the financial account to which the operations belong
     * @param $dateFrom
     * first day in the filtered interval
     * @param $dateTo
     * last day in the filtered interval
     * @return string
     * the generated file name
     */
    private function generateExportName(Account $account, Carbon $dateFrom, Carbon $dateTo)
    {
        $title = $account->getSanitizedTitle();
        $from = $this->generateFromString($dateFrom);
        $to = $this->generateToString($dateTo);

        return "{$title}_export{$from}{$to}.csv";
    }

    /**
     * If the filtering interval is bound by the earliest date, generates a string describing that date.
     * Otherwise, generates an empty string.
     *
     * @param Carbon $dateFrom
     * the earliest date of the interval
     * @return string
     * the generated string
     */
    private function generateFromString(Carbon $dateFrom)
    {
        if ($dateFrom == Date::minValue())
            $from = '';
        else
        {
            $fromClause = trans('files.from');
            $from = "_{$fromClause}_{$this->formatDate($dateFrom)}";
        }
        return $from;
    }

    /**
     * If the filtering interval is bound by the latest date, generates a string describing that date.
     * Otherwise, generates an empty string.
     *
     * @param Carbon $dateTo
     * the latest date of the interval
     * @return string
     * the generated string
     */
    private function generateToString(Carbon $dateTo)
    {
        if ($dateTo == Date::maxValue())
            $to = '';
        else
        {
            $toClause = trans('files.to');
            $to = "_{$toClause}_{$this->formatDate($dateTo)}";
        }
        return $to;
    }

    /**
     * Creates a string in the 'd-m-Y' format from a date object.
     *
     * @param Carbon $date
     * the date to be formatted
     * @return string
     * the formatted string
     */
    private function formatDate(Carbon $date)
    {
        return Date::parse($date)->format('d-m-Y');
    }

    /**
     * Writes a CSV file into output file stream, containing data about financial operations.
     *
     * @param Collection $operations
     * collection of financial operations
     * @return false|resource
     * stream containing the exported file
     */
    private function generateCSVfile(Collection $operations)
    {
        $columns = [
            'ID', 'Account ID', 'Title', 'Date', 'Operation type', 'Subject', 'Sum', 'Attachment', 'Checked', 'SAP ID'
        ];
        $stream = fopen('php://output', 'w');
        fputcsv($stream,$columns,';');

        foreach ($operations as $op) fputcsv($stream,$op->getExportData(),';');

        fclose($stream);
        return $stream;
    }
}
