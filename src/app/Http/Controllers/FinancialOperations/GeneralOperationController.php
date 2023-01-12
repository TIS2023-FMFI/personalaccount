<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Exceptions\DatabaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialOperations\CreateOrUpdateOperationRequest;
use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\Lending;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Parent class containing general functions useful for both 'create operation' and 'update operation' controllers.
 */
class GeneralOperationController extends Controller
{

    /**
     * Saves a financial operation's attachment file if it is contained in the given request.
     *
     * @param Account $account
     * the account to which the operation belongs
     * @param CreateOrUpdateOperationRequest $request
     * a HTTP request to create/update an operation
     * @return string
     * path to the saved file, or null if the request doesn't contain a file
     */
    protected function saveAttachmentFileFromRequest(Account $account, CreateOrUpdateOperationRequest $request)
    {
        $file = $request->file('attachment');
        if ($file)
            return $this->saveAttachment($account->user, $file);
        return null;
    }

    /**
     * Saves the given file as an attachment for a financial operation.
     *
     * @param $user
     * the user to which the financial operation belongs
     * @param $file
     * the file to be saved
     * @return string
     * the path to the saved file
     */
    private function saveAttachment(User $user, UploadedFile $file) : string
    {
        $dir = FinancialOperation::getAttachmentsDirectoryPath($user);
        return Storage::putFile($dir, $file);
    }

    /**
     * Inserts a lending record related to a financial operation into the database. If that operation already
     * has a lending record, the lending is instead updated with the new data.
     *
     * @param FinancialOperation $operation
     * the operation associated with the lending
     * @param CreateOrUpdateOperationRequest $request
     * the request containing data about the lending
     * @return void
     * @throws DatabaseException
     */
    protected function upsertLending(FinancialOperation $operation, CreateOrUpdateOperationRequest $request)
    {
        $validatedData = $this->getValidatedLendingUpsertData($operation, $request);

        $lending = Lending::updateOrCreate(
            ['id' => $operation->id],
            $validatedData
        );
        if (!$lending->exists)
            throw new DatabaseException('The lending wasn\'t created.');
    }
    
    /**
     * Extracts lending data from a request and validates them.
     * 
     * @param FinancialOperation $operation
     * the operation associated with the lending
     * @param CreateOrUpdateOperationRequest $request
     * the request from which to extract the data
     * @return array
     * the validated data
     * @throws ValidationException
     */
    private function getValidatedLendingUpsertData(
        FinancialOperation $operation, CreateOrUpdateOperationRequest $request
    ) {
        $expectedReturn = $request->validated('expected_date_of_return');
        $previousLending = FinancialOperation::find(
            $request->validated('previous_lending_id')
        );

        if (!$previousLending)
            return ['expected_date_of_return' => $expectedReturn];

        if ($operation->date->lt($previousLending->date))
            throw ValidationException::withMessages([
                'date' => trans('validation.repayment_invalid_date')
            ]);

        return ['previous_lending_id' => $previousLending->id];
    }
}
