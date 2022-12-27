<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialAccounts\DeleteOperationRequest;
use App\Http\Requests\FinancialAccounts\FilterOperationsRequest;
use App\Http\Requests\FinancialAccounts\MarkOperationRequest;
use App\Models\Account;
use App\Models\FinancialOperation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Request;

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
     * Handles the request to get the account detail page. Returns a view filled with the operations belonging to the given account.
     * The operations are paginated and can be filtered by date by GET parameters 'from' (first date in the interval) and 'to' ('last date').
     *
     * @param $id
     * @param Request $request
     * @return Application|Factory|View
     */
    public function show($id, Request $request)
    {
        $account = Account::findOrFail($id);
        $dateFrom = $this->getDateFromRequestOrMin($request, 'from');
        $dateTo = $this->getDateFromRequestOrMax($request, 'to');

        $incomes = $account->operationsBetween($dateFrom, $dateTo)->incomes()->sum('sum');
        $expenses = $account->operationsBetween($dateFrom, $dateTo)->expenses()->sum('sum');
        $operations = $account->operationsBetween($dateFrom, $dateTo)->orderBy('date', 'desc')->paginate($this->perPage())->withQueryString();

        return view('finances.account', [
            'account' => $account,
            'operations' => $operations,
            'incomes_total' => $incomes,
            'expenses_total' => $expenses,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ]);
    }

    /**
     * Returns a date taken from the GET parameter of a given request, specified by the $key parameter.
     * If the parameter isn't present, the minimal date is returned instead.
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Support\Carbon
     */
    private function getDateFromRequestOrMin(Request $request, $key)
    {
        $date = $request->query($key);
        if ($date) return Date::create($date);
        return Date::minValue();
    }

    /**
     * Returns a date taken from the GET parameter of a given request, specified by the $key parameter.
     * If the parameter isn't present, the maximal date is returned instead.
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Support\Carbon
     */
    private function getDateFromRequestOrMax(Request $request, $key)
    {
        $date = $request->query($key);
        if ($date) return Date::create($date);
        return Date::maxValue();
    }

    /**
     * Handles the request to filter operations by date. Reroutes the request to get the account detail view with
     * GET parameters based on the request's validated data.
     *
     * @param $id
     * @param FilterOperationsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function filterOperations($id, FilterOperationsRequest $request)
    {
        return redirect()->route('account-detail', [
            'id' => $id,
            'from' => $request->validated('date_from'),
            'to' => $request->validated('date_to')
        ]);
    }

    /*public function downloadExport($id, Request $request)
    {
        $account = Account::findOrFail($id);
        $dateFrom = $this->getDateFromRequestOrMin($request, 'from');
        $dateTo = $this->getDateFromRequestOrMax($request, 'to');

        $operations = $account->operationsBetween($dateFrom, $dateTo)->orderBy('date', 'desc')->get();

        return response()->streamDownload(function() use ($operations){ $this->generateCSVfile($operations); }, 'export.csv');
    }

    public function generateCSVfile($operations){
        $columns = ['ID', 'Account ID', 'Title', 'Date', 'Operation type', 'Subject', 'Sum', 'Attachment', 'Checked', 'SAP ID'];
        $stream = fopen('php://output', 'w');
        fputcsv($stream,$columns,';');

        foreach ($operations as $op)
        {
            fputcsv($stream,$op->getExportData(),';');
        }

        fclose($stream);
        return $stream;
    }

    public function deleteOperation(DeleteOperationRequest $request)
    {
        $operation = FinancialOperation::find($request->validated('id'));
        $operation->deleteAttachmentIfExists();
        if ($operation->delete()) return response(trans('finance_accounts.new.success'), 200);
        return response(trans('finance_accounts.new.failed'), 500);
    }

    public function markOperationAsChecked(MarkOperationRequest $request)
    {
        if (FinancialOperation::find($request->validated('id'))->update(['checked' => true]))
        {
            return response(trans('finance_accounts.new.success'), 200);
        }
        return response(trans('finance_accounts.new.failed'), 500);
    }*/

}
