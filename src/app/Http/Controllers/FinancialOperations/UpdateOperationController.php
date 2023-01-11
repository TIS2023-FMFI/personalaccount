<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Exceptions\DatabaseException;
use App\Exceptions\StorageException;
use App\Http\Helpers\DBTransaction;
use App\Http\Requests\FinancialOperations\CheckOrUncheckOperationRequest;
use App\Http\Requests\FinancialOperations\CreateOrUpdateOperationRequest;
use App\Models\FinancialOperation;
use App\Models\OperationType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Manages updates of financial operations, including checking and unchecking.
 */
class UpdateOperationController extends GeneralOperationController
{

    /**
     * Handles the request to update a financial operation.
     *
     * @param FinancialOperation $operation
     * the operation to be updated
     * @param CreateOrUpdateOperationRequest $request
     * the HTTP request to update the operation
     * @return Application|ResponseFactory|Response
     */
    public function handleUpdateOperationRequest(FinancialOperation $operation, CreateOrUpdateOperationRequest $request)
    {
        try {
            $newAttachment = $this->saveAttachmentFileFromRequest($operation->account, $request);
            $oldAttachment = $operation->attachment;

            $this->runUpdateOperationTransaction($operation, $request,  $oldAttachment, $newAttachment);
        }
        catch (Exception $e) {
            return response(trans('financial_operations.update.failure'), 500);
        }
        return response(trans('financial_operations.update.success'));
    }

    /**
     * Runs a database transaction in which a financial operation is updated.
     *
     * @param FinancialOperation $operation
     * the operation to be updated
     * @param CreateOrUpdateOperationRequest $request
     * the HTTP request to update the operation
     * @param string $oldAttachment
     * path to the operation's original attachment file (if there was one)
     * @param string $newAttachment
     * path to the operation's updated attachment file (if there is one)
     * @throws Exception
     */
    private function runUpdateOperationTransaction(FinancialOperation $operation, CreateOrUpdateOperationRequest $request,
                                                   string $oldAttachment, string $newAttachment)
    {
        $updateOperationTransaction = new DBTransaction(
            fn () => $this->updateOperation($operation, $request, $oldAttachment, $newAttachment),
            fn () => Storage::delete($newAttachment)
        );

        $updateOperationTransaction->run();
    }

    /**
     * Updates the operation, creating or deleting attachment files and associated lending records if needed.
     *
     * @param FinancialOperation $operation
     * the operation to be updated
     * @param CreateOrUpdateOperationRequest $request
     * the HTTP request to update the operation
     * @param string $oldAttachment
     * path to the operation's original attachment file (if there was one)
     * @param string $newAttachment
     * path to the operation's updated attachment file (if there is one)
     * @throws DatabaseException
     * @throws StorageException
     */
    private function updateOperation(FinancialOperation $operation, CreateOrUpdateOperationRequest $request,
                                     string $oldAttachment, string $newAttachment)
    {
        if ($this->typeChangedFromLending($operation, $request))
            $operation->deleteLending();

        $this->updateOperationRecord($operation, $request, $newAttachment);

        $operation->refresh();

        if ($operation->isLending())
            $this->upsertLending($operation->id, $request);

        if ($newAttachment)
            Storage::delete($oldAttachment);
    }

    /**
     * Updates the financial operation's record in the database
     *
     * @param FinancialOperation $operation the operation to be updated
     * @param CreateOrUpdateOperationRequest $request the HTTP request to update the operation
     * @param string $newAttachment path to the operation's updated attachment file (if there is one)
     * @throws DatabaseException
     */
    private function updateOperationRecord(FinancialOperation $operation, CreateOrUpdateOperationRequest $request,
                                           string $newAttachment)
    {
        $validatedData = $request->validated();

        if (! $operation->update([
            'title' => $validatedData['title'],
            'date' => $validatedData['date'],
            'operation_type_id' => $validatedData['operation_type_id'],
            'subject' => $validatedData['subject'],
            'sum' => $validatedData['sum'],
            'attachment' => ($newAttachment) ? $newAttachment : $operation->attachment
        ]))
            throw new DatabaseException('The operation wasn\'t updated.');
    }

    /**
     * Finds out whether the finacnial operation was a lending originally, but it's requested to change
     * into a non-lending type.
     *
     * @param FinancialOperation $operation
     * the operation to be updated
     * @param CreateOrUpdateOperationRequest $request
     * the HTTP request to update the operation
     * @return bool
     */
    private function typeChangedFromLending(FinancialOperation $operation, CreateOrUpdateOperationRequest $request)
    {
        $newTypeId = $request->validated('operation_type_id');

        $oldType = $operation->operationType;
        if ($newTypeId == $oldType->id)
            return false;

        $newType = OperationType::findOrFail($newTypeId);

        return $oldType->lending && ! $newType->lending;
    }

    /**
     * Handles the request to mark/unmark a financial operation as checked by the user.
     *
     * @param FinancialOperation $operation
     * the operation to be (un)checked
     * @param CheckOrUncheckOperationRequest $request
     * $request the request to (un)check the operation
     * @return Application|ResponseFactory|Response
     * a response containing information about this operation's result
     */
    public function checkOrUncheckOperation(FinancialOperation $operation, CheckOrUncheckOperationRequest $request)
    {
        if ($operation->isLending())
            return response(trans('financial_operations.invalid_check'), 422);

        if ($operation->update(['checked' => $request->validated('checked')]))
            return response(trans('financial_operations.edit.success'));

        return response(trans('financial_operations.edit.failure'), 500);
    }
}
