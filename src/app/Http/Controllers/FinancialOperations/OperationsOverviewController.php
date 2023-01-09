<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Base\DateRequest;
use App\Models\Account;
use App\Models\FinancialOperation;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Manages the 'account detail' screen and all functionality available directly from that screen.
 */
class OperationsOverviewController extends Controller
{
    /**
     * @var int - number of operations to be shown on one page
     */
    public static int $perPage = 15;

    /**
     * shortcut for the $perPage static variable
     *
     * @return int
     */
    public function perPage()
    {
        return OperationsOverviewController::$perPage;
    }

    /**
     * Handles the request to get the account detail page. Returns a view filled with the operations belonging
     * to the given account. The operations are paginated and can be filtered by date by GET parameters 'from'
     * (first date in the interval) and 'to' ('last date').
     *
     * @param Account $account - route parameter
     * @param DateRequest $request - the GET request containing query parameters
     * @return Application|Factory|View
     */
    public function show(Account $account, DateRequest $request)
    {
        $dateFrom = $this->getFromDateOrMin($request);
        $dateTo = $this->getToDateOrMax($request);

        $incomes = $account->operationsBetween($dateFrom, $dateTo)->incomes()->sum('sum');
        $expenses = $account->operationsBetween($dateFrom, $dateTo)->expenses()->sum('sum');
        $operations = $account->operationsBetween($dateFrom, $dateTo)->orderBy('date', 'desc')->paginate($this->perPage())->withQueryString();

        return view('finances.account', [
            'account' => $account,
            'operations' => $operations,
            'incomes_total' => $incomes,
            'expenses_total' => $expenses
        ]);
    }

    /**
     * Returns a date taken from the query parameter of a given request, specified by the $key parameter.
     * If the parameter isn't present, the minimal possible date is returned instead.
     *
     * @param DateRequest $request
     * @return Carbon
     */
    private function getFromDateOrMin(DateRequest $request)
    {
        $date = $request->validated('from');
        if ($date) return Date::create($date);
        return Date::minValue();
    }

    /**
     * Returns a date taken from the query parameter of a given request, specified by the $key parameter.
     * If the parameter isn't present, the maximal possible date is returned instead.
     *
     * @param DateRequest $request
     * @return Carbon
     */
    private function getToDateOrMax(DateRequest $request)
    {
        $date = $request->validated('to');
        if ($date) return Date::create($date);
        return Date::maxValue();
    }

    /**
     * Handles a request to download a CSV export for the given account.
     *
     * @param Account $account
     * @param DateRequest $request
     * @return StreamedResponse
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
     * Generates a name for the CSV export file, containing the name of the account. If the dates in the export are limited,
     * the bounding dates are present in the name as well.
     *
     * @param $account
     * @param $dateFrom
     * @param $dateTo
     * @return string
     */
    private function generateExportName($account, $dateFrom, $dateTo)
    {
        $name = $this->removeSpecialCharacters($account->title);

        if (!$dateFrom || $dateFrom == Date::minValue()) $from = '';
        else $from = "_from_{$this->simplifyDate($dateFrom)}";

        if (!$dateTo || $dateTo == Date::maxValue()) $to = '';
        else $to = "_to_{$this->simplifyDate($dateTo)}";

        return "export_{$name}{$from}{$to}.csv";
    }

    /**
     * Returns a formatted string containing only the date from the given date/dateTime.
     *
     * @param $date
     * @return string
     */
    private function simplifyDate($date)
    {
        return Date::parse($date)->format('d-m-Y');
    }

    /**
     * Writes a CSV file into output file stream, containing data about financial operations.
     *
     * @param $operations - collection of financial operations
     * @return false|resource
     */
    private function generateCSVfile($operations)
    {
        $columns = ['ID', 'Account ID', 'Title', 'Date', 'Operation type', 'Subject', 'Sum', 'Attachment', 'Checked', 'SAP ID'];
        $stream = fopen('php://output', 'w');
        fputcsv($stream,$columns,';');

        foreach ($operations as $op) fputcsv($stream,$op->getExportData(),';');

        fclose($stream);
        return $stream;
    }
}
