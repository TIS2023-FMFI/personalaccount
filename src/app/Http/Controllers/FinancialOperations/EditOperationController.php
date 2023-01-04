<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Http\Requests\FinancialOperations\EditOperationRequest;
use App\Models\FinancialOperation;
use App\Models\Lending;
use App\Models\OperationType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\throwException;

/**
 * Manages functionality of the 'edit operation' modal.
 */
class EditOperationController extends GeneralOperationController
{
    /**
     * Handles the request to edit a financial operation. Manages updating the operation itself, its related
     * lending record and its attachment file if there are any.
     *
     * @param $operation_id - route parameter
     * @param EditOperationRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function handleEditOperationRequest($operation_id, EditOperationRequest $request)
    {
        $operation = FinancialOperation::findOrFail($operation_id);

        $old_attachment = $operation->attachment;
        $new_attachment = null;

        $file = $request->file('attachment');
        if ($file) $new_attachment = $this->saveAttachment($operation->getUserId(), $file);

        DB::beginTransaction();
        try
        {
            if ($this->typeChangedFromLending($request, $operation)) $this->deleteLending($operation);
            $this->updateOperation($request, $operation, ($file) ? $new_attachment : null);

            $operation->refresh();
            if ($operation->isLending()) $this->upsertLending($request, $operation->id);

            if ($file) $this->deleteFileIfExists($old_attachment);
        }
        catch (Exception $e)
        {
            $this->deleteFileIfExists($new_attachment);
            DB::rollBack();
            //return response($e->getMessage(), 500); //for debugging purposes
            return response(trans('finance_operations.edit.failure'), 500);
        }

        DB::commit();
        return response(trans('finance_operations.edit.success'), 200);
    }

    /**
     * Changes the data in the DB record for the given operation according to the request.
     *
     * @param EditOperationRequest $request
     * @param $operation
     * @param $attachment - updated path to the operation's attachment file
     */
    private function updateOperation(EditOperationRequest $request, $operation, $attachment)
    {
        if (! $operation->update([
            'title' => $request->validated('title'),
            'date' => $request->validated('date'),
            'operation_type_id' => $request->validated('operation_type_id'),
            'subject' => $request->validated('subject'),
            'sum' => $request->validated('sum'),
            'attachment' => ($attachment) ? $attachment : $operation->attachment,
        ])) throwException(new Exception('The operation wasn\'t updated.'));
    }

    /**
     * Returns 'true' if the given operation was a lending originally, but it's requested to change
     * into a non-lending type. Otherwise, returns 'false'.
     *
     * @param EditOperationRequest $request
     * @param $operation
     * @return bool
     */
    private function typeChangedFromLending(EditOperationRequest $request, $operation)
    {
        $newTypeId = $request->validated('operation_type_id');
        if (!$newTypeId) return false;

        $oldType = $operation->operationType;
        $newType = OperationType::findOrFail($newTypeId);
        return $oldType->lending && ! $newType->lending;
    }

    /**
     * Deletes the lending record related to the given operation, if there is any.
     *
     * @param $operation
     * @return void
     */
    private function deleteLending($operation)
    {
        if (! Lending::destroy($operation->lending->id))
            throwException(new Exception('The lending wasn\'t deleted.'));
    }

}
