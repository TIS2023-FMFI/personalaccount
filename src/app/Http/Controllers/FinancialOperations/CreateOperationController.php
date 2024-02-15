<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Exceptions\DatabaseException;
use App\Http\Helpers\DBTransaction;
use App\Http\Helpers\FileHelper;
use App\Http\Requests\FinancialOperations\CreateOperationRequest;
use App\Http\Requests\FinancialOperations\CreateRepaymentRequest;
use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\Lending;
use App\Models\OperationType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
/**
 * Manages creation of financial operations.
 */
class CreateOperationController extends GeneralOperationController
{
    /**
     * Prepares the data necessary to populate the form handling operation creation.
     *
     * @param Account $account
     * the account with which the new operation will be associated
     * @return array
     * an array containing the supported operation types
     */
    public function getFormData(Account $account)
    {
        $user = $account->user->first();
        return [
            'operation_types' => OperationType::userAssignable()->get(),
            'unrepaid_lendings' => FinancialOperation::unrepaidLendings()->where('account_sap_id', '=', $user->pivot->id)->get()
        ];
    }

    /**
     * Handles the request to create a new financial operation.
     *
     * @param Account $account
     * the account with which to associate the operation
     * @param CreateOperationRequest $request
     * the request to create the operation
     * @return Application|ResponseFactory|Response
     * a response containing information about this operation's result
     */
    public function create(Account $account, CreateOperationRequest $request)
    {

        DB::enableQueryLog();
        $type = OperationType::findOrFail($request->validated('operation_type_id'));

        if ($type->repayment)
            return response(trans('financial_operations.create.failure'), 500);

        return $this->createOperationFromData($account, $request->validated());
    }

    /**
     * Handles a request to create a new repayment operation.
     *
     * @param Lending $lending
     * the lending with which to associate the repayment
     * @param CreateRepaymentRequest $request
     * the request containing the repayment data
     * @return Application|ResponseFactory|Response
     * a response containing information about this operation's result
     */
    public function createRepayment(Lending $lending, CreateRepaymentRequest $request)
    {
        $lendingOperation = $lending->operation;

        if ($lendingOperation->isRepayment())
            return response(trans('financial_operations.create.failure'), 500);

        $account = $lendingOperation->account();
        $data = $request->prepareValidatedOperationData($lendingOperation);

        return $this->createOperationFromData($account, $data);
    }

    /**
     * Creates a new financial operation from raw data.
     *
     * @param Account $account
     * the account with which to associate the operation
     * @param array $data
     * the data based on which to create the operation
     * (should contain values for all attributes in CreateOperationRequest and
     * optionally values for all attributes in CreateRepaymentRequest)
     * @return Application|ResponseFactory|Response
     * a response containing information about this operation's result
     */
    private function createOperationFromData(Account $account, array $data)
    {
        try {
            $attachment = $this->saveAttachment($account, $data);
            $this->createOperationWithinTransaction($account, $data, $attachment);
        } catch (Exception $e) {
            Log::debug('Creating financial operation failed, error: {e}', ['e' => $e]);
            if ($e instanceof ValidationException)
                throw $e;
            return response(trans('financial_operations.create.failure'), 500);
        }

        return response(trans('financial_operations.create.success'), 201);
    }

    /**
     * Runs a database transaction in which a financial operation is created.
     *
     * @param Account $account
     * the account with which to associate the operation
     * @param array $data
     * the data based on which to create the operation
     * @param string|null $attachment
     * the path to the operation's attachment file (if any)
     * @return void
     * @throws Exception
     */
    private function createOperationWithinTransaction(
        Account $account, array $data, string|null $attachment
    ) {
        $createRecordTransaction = new DBTransaction(
            fn () => $this->createOperationAndLendingRecord($account, $data, $attachment),
            fn () => FileHelper::deleteFileIfExists($attachment)
        );

        $createRecordTransaction->run();
    }

    /**
     * Creates a record for the new operation, and, if needed, its associated
     * lending record.
     *
     * @param Account $account
     * the account with which to associate the operation
     * @param array $data
     * the data based on which to create the operation
     * @param string|null $attachment
     * the path to the operation's attachment file (if any)
     * @return void
     * @throws DatabaseException
     */
    private function createOperationAndLendingRecord(
        Account $account, array $data, string|null $attachment
    )
    {
        $operation = $this->createOperationRecord($account, $data, $attachment);
        Log::debug("Created an operation {e}", [ 'e' => $operation]);
        Log::debug("Is the operation a lending? {e}", [ 'e' => $operation->isLending()]);
        if ($operation->isLending())
        {
            $this->upsertLending($operation, $data);
        }

    }

    /**
     * Creates a new financial operation record in the database.
     *
     * @param Account $account
     * the account with which to associate the operation
     * @param array $data
     * the data based on which to create the operation
     * @param string|null $attachment
     * the path to the operation's attachment file (if any)
     * @return FinancialOperation
     * the model representing the created operation
     * @throws DatabaseException
     */
    private function createOperationRecord(
        Account $account, array $data, string|null $attachment
    ) {
        unset($data['expected_date_of_return']);
        unset($data['previous_lending_id']);
        $user = $account->user->first();
        $accountUserId = $user->pivot->id;
        $recordData = array_merge($data, ['attachment' => $attachment, 'account_user_id' => $accountUserId]);
        Log::debug('Creating financial operation from data: {data}', ['data' => $recordData]);
        $operation = $account->operations()->updateOrCreate($recordData);

        if (!$operation->exists)
            throw new DatabaseException('The operation wasn\'t created.');

        return $operation;
    }
}
