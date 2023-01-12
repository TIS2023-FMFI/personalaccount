<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Exceptions\DatabaseException;
use App\Http\Helpers\DBTransaction;
use App\Http\Helpers\FileHelper;
use App\Http\Requests\FinancialOperations\CreateOrUpdateOperationRequest;
use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\OperationType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Manages creation of financial operations.
 */
class CreateOperationController extends GeneralOperationController
{
    /**
     * Prepares the data necessary to populate the form handling operation creation.
     * 
     * @param Account $operation
     * the account with which the new operation will be associated
     * @return array
     * an array containing information about the supported operation types
     * and a list of unrepaid lendings associated with the account under which
     * the new operation will be created
     */
    public function getFormData(Account $account)
    {
        $operationTypes = OperationType::all();
        $unrepaidLendings = $account
                                ->financialOperations()
                                ->unrepaidLendings()
                                ->get();

        return [
            'operation_types' => $operationTypes,
            'unrepaid_lendings' => $unrepaidLendings,
        ];
    }
    
    /**
     * Handles the request to create a new financial operation.
     *
     * @param Account $account
     * financial account to which the operation belongs
     * @param CreateOrUpdateOperationRequest $request
     * HTTP request to create the operation
     * @return Application|ResponseFactory|Response
     * a response containing information about this operation's result
     */
    public function create(Account $account, CreateOrUpdateOperationRequest $request)
    {
        try {
            $attachment = $this->saveAttachmentFileFromRequest($account, $request);
            $this->runCreateOperationTransaction($account, $request, $attachment);
        } catch (Exception $e) {
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
     * financial account to which the operation belongs
     * @param CreateOrUpdateOperationRequest $request
     * HTTP request to create the operation
     * @param string|null $attachment
     * path to the operation's attachment file
     * @return void
     * @throws Exception
     */
    private function runCreateOperationTransaction(Account $account, CreateOrUpdateOperationRequest $request,
                                                   string|null  $attachment)
    {
        $createRecordTransaction = new DBTransaction(
            fn () => $this->createOperation($account, $request, $attachment),
            fn () => FileHelper::deleteFileIfExists($attachment)
        );

        $createRecordTransaction->run();
    }

    /**
     * Creates a record for the new operation, and, if needed, its associated lending record.
     *
     * @param Account $account
     * financial account to which the operation belongs
     * @param CreateOrUpdateOperationRequest $request
     * HTTP request to create the operation
     * @param string|null $attachment
     * path to the operation's attachment file
     * @return void
     * @throws DatabaseException
     */
    private function createOperation(Account $account, CreateOrUpdateOperationRequest $request, string|null $attachment)
    {
        $operation = $this->createOperationRecord($account, $request, $attachment);
        if ($operation->isLending())
            $this->upsertLending($operation, $request);
    }

    /**
     * Creates a new financial operation record in the database.
     *
     * @param Account $account
     * financial account to which the operation belongs
     * @param CreateOrUpdateOperationRequest $request
     * HTTP request to create the operation
     * @param string|null $attachment
     * path to the operation's attachment file
     * @return FinancialOperation
     * model representing the created operation
     * @throws DatabaseException
     */
    private function createOperationRecord(Account $account, CreateOrUpdateOperationRequest $request, string|null $attachment)
    {
        $validatedData = $request->validated();

        $operation = $account->financialOperations()->create([
            'account_id' => $account->id,
            'title' => $validatedData['title'],
            'date' => $validatedData['date'],
            'operation_type_id' => $validatedData['operation_type_id'],
            'subject' => $validatedData['subject'],
            'sum' => $validatedData['sum'],
            'attachment' => $attachment,
        ]);
        if (!$operation->exists)
            throw new DatabaseException('The operation wasn\'t created.');
        return $operation;
    }
}
