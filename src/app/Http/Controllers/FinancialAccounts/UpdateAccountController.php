<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Exceptions\DatabaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialAccounts\CreateAccountRequest;
use App\Http\Requests\FinancialAccounts\UpdateAccountRequest;
use App\Models\Account;

/**
 * A controller responsible for updating financial accounts.
 *
 * This controller provides methods to:
 *      - update a financial account
 */
class UpdateAccountController extends Controller
{
    /**
     * Handle a request to update a financial account.
     *
     * @param UpdateAccountRequest $request
     * the request containing the updated version of account data
     * @param \App\Models\Account $account
     * the account update
     * @return \Illuminate\Http\Response
     * a response containing the information about the result of this operation
     * presented as a plain-text message
     */
    public function update(UpdateAccountRequest $request, Account $account)
    {
        $data = $this->extractAccountData($request);

        try {
            $this->updateAccountRecord($account, $data);
        } catch (DatabaseException $e) {
            return response(trans('financial_accounts.update.failed'), 500);
        }

        return response(trans('financial_accounts.update.success'));
    }

    /**
     * Extract account data from a request.
     *
     * @param UpdateAccountRequest $request
     * the request from which to extract data
     * @return array
     * the extracted data
     */
    private function extractAccountData(UpdateAccountRequest $request)
    {
        return [
            'title' => $request->validated('title'),
            'sap_id' => $request->validated('sap_id')
        ];
    }

    /**
     * Update a financial account record.
     *
     * @param \App\Models\Account $account
     * the record to update
     * @param array $data
     * the updated version of account data (except for user_id)
     * @throws \App\Exceptions\DatabaseException
     * thrown if the record could not be updated
     * @return void
     */
    private function updateAccountRecord(Account $account, array $data)
    {
        if (!$account->update($data)) {
            throw new DatabaseException('Record not updated.');
        }
    }
}
