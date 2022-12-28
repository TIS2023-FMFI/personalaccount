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

class CreateOperationController extends GeneralOperationController
{

    public function handleCreateOperationRequest(CreateOperationRequest $request)
    {
        $account = Account::findOrFail($request->validated('account_id'));

        $attachmentPath = null;
        $file = $request->file('attachment');

        if ($file){
            $dir = $this->generateAttachmentDirectory($account->getUserId());
            $filename = $this->generateAttachmentName($dir);
            $attachmentPath = sprintf('%s/%s', $dir, $filename);
        }

        DB::beginTransaction();
        try{
            $operation = $this->createOperation($request, $account, $attachmentPath);
            if ($file) Storage::putFileAs($dir, $file, $filename);
            if ($operation->isLending()) $this->upsertLending($request, $operation->id);
        }
        catch (Exception $e){
            $this->deleteFileIfExists($attachmentPath);
            DB::rollBack();
            return response($e->getMessage(), 500);
        }
        DB::commit();
        return response(trans('finance_accounts.new.success'), 201);
    }

    public function generateAttachmentDirectory($userId): string
    {
        return sprintf('user_%d/attachments', $userId);
    }

    public function generateAttachmentName($directory): string
    {
        $num = 0;
        while(true){
            $name = sprintf('attachment_%04d', $num);
            if (!Storage::exists($directory.'/'.$name)) return $name;
            $num++;
        }
    }

    public function createOperation($request, $account, $attachment){
        $operation = $account->financialOperations()->create([
            'account_id' => $account->id,
            'title' => $request->validated('title'),
            'date' => $request->validated('date'),
            'operation_type_id' => $request->validated('operation_type_id'),
            'subject' => $request->validated('subject'),
            'sum' => $request->validated('sum'),
            'attachment' => $attachment,
        ]);
        if (!$operation->exists) throwException(new Exception());
        return $operation;
    }



    public function upsertLending($request, $id)
    {

        $lending = Lending::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'expected_date_of_return' => $request->validated('expected_date_of_return'),
                'previous_lending_id' => $request->validated('previous_lending_id'),
            ]
        );
        if (!$lending->exists) throwException(new Exception());

    }
}
