<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Http\Requests\FinancialOperations\CreateOrUpdateOperationRequest;
use App\Models\Account;
use App\Models\FinancialOperation;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * Manages the functionality of the 'create operation' modal.
 */
class CreateOperationController extends GeneralOperationController
{

    /**
     * Handles the request to create a new financial operation.
     *
     * @param Account $account - route parameter
     * @param CreateOrUpdateOperationRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function handleCreateOperationRequest(Account $account, CreateOrUpdateOperationRequest $request)
    {
        $attachmentPath = null;
        $file = $request->file('attachment');
        if ($file) $attachmentPath = $this->saveAttachment($account->user_id, $file);

        DB::beginTransaction();
        try
        {
            $operation = $this->createOperation($request, $account, $attachmentPath);
            if ($operation->isLending()) $this->upsertLending($request, $operation->id);
        }
        catch (Exception $e)
        {
            $this->deleteFileIfExists($attachmentPath);
            DB::rollBack();
            // return \response($e->getMessage(), 500); //for debugging purposes
            return response('financial_operations.create.failure', 500);
        }
        DB::commit();
        return response(trans('financial_operations.create.success'), 201);
    }


    /**
     * Creates a new operation DB record using the data from the request. Returns the created operation model.
     *
     * @param $request
     * @param $account
     * @param $attachment
     * @return mixed
     */
    private function createOperation($request, $account, $attachment)
    {
        $operation = $account->financialOperations()->create([
            'account_id' => $account->id,
            'title' => $request->validated('title'),
            'date' => $request->validated('date'),
            'operation_type_id' => $request->validated('operation_type_id'),
            'subject' => $request->validated('subject'),
            'sum' => $request->validated('sum'),
            'attachment' => $attachment,
        ]);
        if (!$operation->exists) throw new Exception('The operation wasn\'t created.');
        return $operation;
    }
}
