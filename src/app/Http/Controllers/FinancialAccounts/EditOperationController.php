<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Http\Requests\FinancialOperations\EditOperationRequest;
use App\Models\FinancialOperation;
use App\Models\Lending;
use App\Models\OperationType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\throwException;

class EditOperationController extends GeneralOperationController
{
    /**
     * Returns the "edit operation" view, filled with current data about the operation.
     *
     * @param $operation_id
     * @return Application|Factory|View
     */
    public function show($operation_id){

        $operation = FinancialOperation::findOrFail($operation_id);
        $lending = $operation->lending;

        return view('finances.modals.edit_operation', [
            'operation' => $operation,
            'lending' => $lending
        ]);

    }

    /**
     * Handles the request to edit a financial operation. Manages updating the operation itself, its related
     * lending record and its attachment file if there are any.
     *
     * @param EditOperationRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function handleEditOperationRequest(EditOperationRequest $request)
    {
        $operation = FinancialOperation::find($request->validated('id'));

        $old_attachment = $operation->attachment;
        $new_attachment = null;

        $file = $request->file('attachment');
        if ($file) $new_attachment = $this->saveAttachment($operation->getUserId(), $file);

        DB::beginTransaction();
        try{

            if ($this->typeChangedFromLending($request, $operation)) $this->deleteLending($operation);
            $this->updateOperation($request, $operation, ($file) ? $new_attachment : null);

            $operation->refresh();
            if ($operation->isLending()) $this->upsertLending($request, $operation->id);

            if ($file) $this->deleteFileIfExists($old_attachment);

        }
        catch (Exception $e){
            $this->deleteFileIfExists($new_attachment);
            DB::rollBack();
            // return \response($e->getMessage(), 500); //for debugging purposes
            return response(trans('finance_operations.edit.failure'), 500);
        }

        DB::commit();
        return response(trans('finance_operations.edit.success'), 200);

    }

    /**
     * Changes the data in the DB record for the given operation according to the request. If the request doesn't
     * contain values for some columns, the original values are left untouched.
     *
     * @param EditOperationRequest $request
     * @param $operation
     * @param $attachment - updated path to the operation's attachment file
     */
    private function updateOperation(EditOperationRequest $request, $operation, $attachment){

        $title = $request->validated('title');
        $date = $request->validated('date');
        $operationTypeId = $request->validated('operation_type_id');
        $subject = $request->validated('subject');
        $sum = $request->validated('sum');

        if (! $operation->update([
            'title' => ($title) ? $title : $operation->title,
            'date' => ($date) ? $date : $operation->date,
            'operation_type_id' => ($operationTypeId) ? $operationTypeId : $operation->operationType->id,
            'subject' => ($subject) ? $subject : $operation->subject,
            'sum' => ($sum) ? $sum : $operation->sum,
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
    private function typeChangedFromLending(EditOperationRequest $request, $operation): bool
    {
        $newTypeId = $request->validated('operation_type_id');
        if (!$newTypeId) return false;

        $oldType = $operation->operationType;
        $newType = OperationType::findOrFail($newTypeId);
        return $oldType->isLending() && ! $newType->isLending();
    }

    /**
     * Deletes the lending record related to the given operation, if there is any.
     *
     * @param $operation
     * @return void
     */
    private function deleteLending($operation){
        if (! Lending::destroy($operation->lending->id))
            throwException(new Exception('The lending wasn\'t deleted.'));
    }

}
