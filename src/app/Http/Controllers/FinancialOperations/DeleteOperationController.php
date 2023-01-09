<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Models\FinancialOperation;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * Manages the functionality of the 'delete operation' modal.
 */
class DeleteOperationController extends GeneralOperationController
{
    /**
     * Handles the request to delete a financial operation.
     *
     * @param FinancialOperation $operation - route parameter
     * @return Application|ResponseFactory|Response
     */
    public function delete(FinancialOperation $operation)
    {
        $attachment = $operation->attachment;

        DB::beginTransaction();
        try
        {
            if (!$operation->delete()) throw new Exception('The operation wasn\'t deleted.');
            if ($attachment) $this->deleteFileIfExists($attachment);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            //return response($e->getMessage(), 500); //for debugging purposes
            return response(trans('financial_operations.delete.failure'), 500);
        }
        DB::commit();
        return response(trans('financial_operations.delete.success'), 200);
    }
}
