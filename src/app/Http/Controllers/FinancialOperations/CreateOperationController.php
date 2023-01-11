<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Exceptions\DatabaseException;
use App\Http\Helpers\DBTransaction;
use App\Http\Helpers\FileHelper;
use App\Http\Requests\FinancialOperations\CreateOrUpdateOperationRequest;
use App\Models\Account;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

/**
 * Manages creation of financial operations.
 */
class CreateOperationController extends GeneralOperationController
{

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
    public function handleCreateOperationRequest(Account $account, CreateOrUpdateOperationRequest $request)
    {
        try
        {
            $attachment = $this->saveAttachmentFileFromRequest($account, $request);
            $this->runCreateOperationTransaction($account, $request, $attachment);
        }
        catch (Exception $e)
        {
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
     * @param string $attachment
     * path to the operation's attachment file
     * @return void
     * @throws Exception
     */
    private function runCreateOperationTransaction(Account $account, CreateOrUpdateOperationRequest $request,
                                                   string  $attachment)
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
     * @param string $attachment
     * path to the operation's attachment file
     * @return void
     * @throws DatabaseException
     */
    private function createOperation(Account $account, CreateOrUpdateOperationRequest $request, string $attachment)
    {
        $operation = $this->createOperationRecord($account, $request, $attachment);
        if ($operation->isLending())
            $this->upsertLending($operation->id, $request);
    }

    /**
     * Creates a new financial operation record in the database.
     *
     * @param Account $account
     * financial account to which the operation belongs
     * @param CreateOrUpdateOperationRequest $request
     * HTTP request to create the operation
     * @param string $attachment
     * path to the operation's attachment file
     * @return Model
     * model representing the created operation
     * @throws DatabaseException
     */
    private function createOperationRecord(Account $account, CreateOrUpdateOperationRequest $request, string $attachment)
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
