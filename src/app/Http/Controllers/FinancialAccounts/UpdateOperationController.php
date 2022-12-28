<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialOperations\CreateOperationRequest;
use App\Http\Requests\FinancialOperations\UpdateOperationRequest;
use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\Lending;
use App\Models\OperationType;
use Exception;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\throwException;

class UpdateOperationController extends GeneralOperationController
{
    public function show($operation_id){

        $operation = FinancialOperation::findOrFail($operation_id);
        $lending = $operation->lending;

        return view('finances.modals.edit_operation', [
            'operation' => $operation,
            'lending' => $lending
        ]);

    }

    public function handleUpdateOperationRequest(UpdateOperationRequest $request)
    {
        $operation = FinancialOperation::find($request->validated('id'));

        $old_attachment = $operation->attachment;
        $new_attachment = null;
        $file = $request->file('attachment');

        if ($file){
            $dir = $this->generateAttachmentDirectory($operation->getUserId());
            $filename = $this->generateAttachmentName($dir);
            $new_attachment = sprintf('%s/%s', $dir, $filename);
        }

        DB::beginTransaction();

        try{

            if ($this->typeChangedFromLending($request, $operation)) $this->deleteLending($operation);
            $this->updateOperation($request, $operation, ($file) ? $new_attachment : null);

            $operation->refresh();
            if ($operation->isLending()) $this->upsertLending($request, $operation->id);

            if ($file)
            {
                Storage::putFileAs($dir, $file, $filename);
                $this->deleteFileIfExists($old_attachment);
            }

        }
        catch (Exception $e){
            $this->deleteFileIfExists($new_attachment);
            DB::rollBack();
            return response($e->getMessage(), 500);
        }

        DB::commit();
        return response(trans('finance_accounts.new.success'), 200);

    }

    public function updateOperation($request, $operation, $attachment){

        $title = $request->validated('title');
        $date = $request->validated('date');
        $operationTypeId = $request->validated('operation_type_id');
        $subject = $request->validated('subject');
        $sum = $request->validated('sum');

        $operation->update([
            'title' => ($title) ? $title : $operation->title,
            'date' => ($date) ? $date : $operation->date,
            'operation_type_id' => ($operationTypeId) ? $operationTypeId : $operation->operationType->id,
            'subject' => ($subject) ? $subject : $operation->subject,
            'sum' => ($sum) ? $sum : $operation->sum,
            'attachment' => ($attachment) ? $attachment : $operation->attachment,
        ]);
        return $operation;
    }

    public function typeChangedFromLending(UpdateOperationRequest $request, $operation): bool
    {
        $newTypeId = $request->validated('operation_type_id');
        if (!$newTypeId) return false;

        $oldType = $operation->operationType;
        $newType = OperationType::findOrFail($newTypeId);
        return $oldType->isLending() && ! $newType->isLending();
    }

    public function deleteLending($operation){
        Lending::destroy($operation->lending->id);
    }

}
