<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\Lending;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function PHPUnit\Framework\throwException;

/**
 * Manages the 'account detail' screen and all functionality available directly from that screen.
 */
class AccountDetailController extends Controller
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
        return AccountDetailController::$perPage;
    }

    /**
     * Handles the request to get the account detail page. Returns a view filled with the operations belonging
     * to the given account. The operations are paginated and can be filtered by date by GET parameters 'from'
     * (first date in the interval) and 'to' ('last date').
     *
     * @param $account_id - route parameter
     * @param Request $request - the GET request containing query parameters
     * @return Application|Factory|View
     */
    public function show($account_id, Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from']
        ]);

        $account = Account::findOrFail($account_id);
        $dateFrom = $this->getDateFromRequestOrMin($request, 'from');
        $dateTo = $this->getDateFromRequestOrMax($request, 'to');

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
     * @param Request $request
     * @param $key - name of the query parameter
     * @return Carbon
     */
    private function getDateFromRequestOrMin(Request $request, $key)
    {
        $date = $request->query($key);
        if ($date) return Date::create($date);
        return Date::minValue();
    }

    /**
     * Returns a date taken from the query parameter of a given request, specified by the $key parameter.
     * If the parameter isn't present, the maximal possible date is returned instead.
     *
     * @param Request $request
     * @param $key - name of the query parameter
     * @return Carbon
     */
    private function getDateFromRequestOrMax(Request $request, $key)
    {
        $date = $request->query($key);
        if ($date) return Date::create($date);
        return Date::maxValue();
    }

    /**
     * Handles a request to download a CSV export for the given account.
     *
     * @param $account_id
     * @param Request $request
     * @return StreamedResponse
     */
    public function downloadExport($account_id, Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from']
        ]);

        $account = Account::findOrFail($account_id);
        $dateFrom = $this->getDateFromRequestOrMin($request, 'from');
        $dateTo = $this->getDateFromRequestOrMax($request, 'to');

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

    /**
     * Handles the request to delete a financial operation.
     *
     * @param $operation_id - route parameter
     * @return Application|ResponseFactory|Response
     */
    public function deleteOperation($operation_id)
    {
        $operation = FinancialOperation::findOrFail($operation_id);
        $attachment = $operation->attachment;

        DB::beginTransaction();
        try
        {

            if (!$operation->delete()) throwException(new Exception('The operation wasn\'t deleted.'));
            if ($attachment) $this->deleteFileIfExists($attachment);

        }
        catch (Exception $e)
        {
            DB::rollBack();
            //return response($e->getMessage(), 500); //for debugging purposes
            return response(trans('finance_operations.delete.failure'), 500);
        }
        DB::commit();
        return response(trans('finance_operations.delete.success'), 200);
    }

    /**
     * Handles the request to mark a financial operation as checked by the user.
     *
     * @param $operation_id  - route parameter
     * @return Application|ResponseFactory|Response
     */
    public function markOperationAsChecked($operation_id)
    {
        if (Lending::find($operation_id)) return response(trans('finance_operations.check.invalid'), 422);
        $operation = FinancialOperation::findOrFail($operation_id);
        if ($operation->update(['checked' => true])) return response(trans('finance_operations.check.success'), 200);
        return response(trans('finance_operations.check.failure'), 500);
    }

}
