<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Http\Controllers\Controller;
use App\Models\Account;

/**
 * A controller responsible for deleting financial accounts.
 * 
 * This controller provides methods to:
 *      - delete a financial account
 */
class DeleteAccountController extends Controller
{
    /**
     * Handle a request to delete a financial account.
     * 
     * @param \App\Models\Account $account
     * the account to delete
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * a response containing the information about the result of this operation
     * presented as a plain-text message
     */
    public function delete(Account $account)
    {
        if ($account->delete()) {
            return response(trans('financial_accounts.delete.success'), 200);
        }

        return response(trans('financial_accounts.delete.failed'), 500);
    }
}
