<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Exceptions\DatabaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialOperations\CreateOperationRequest;
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
     * @param CreateOperationRequest $request
     * a HTTP request to create/update an operation
     * @return string
     * path to the saved file, or null if the request doesn't contain a file
     */
    protected function saveAttachmentFileFromRequest(Account $account, CreateOperationRequest $request)
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
     * @param CreateOperationRequest $request
     * the request containing data about the lending
     * @return void
     * @throws DatabaseException
     */
    protected function upsertLending(FinancialOperation $operation, CreateOperationRequest $request)
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
     * @param CreateOperationRequest $request
     * the request from which to extract the data
     * @return array
     * the validated data
     * @throws ValidationException
     */
    private function getValidatedLendingUpsertData(
        FinancialOperation $operation, CreateOperationRequest $request
    ) {
        return ($operation->isRepayment())
            ? $this->getValidatedRepaymentUpsertData($operation, $request)
            : $this->getValidatedLoanUpsertData($operation, $request);
    }

    /**
     * Extracts repayment data from a request and validates them.
     * 
     * @param FinancialOperation $operation
     * the operation associated with the lending
     * @param CreateOperationRequest $request
     * the request from which to extract the data
     * @return array
     * the validated data
     * @throws ValidationException
     */
    private function getValidatedRepaymentUpsertData(
        FinancialOperation $operation, CreateOperationRequest $request
    ) {
        $loan = FinancialOperation::findOrFail(
            $request->validated('previous_lending_id')
        );

        $this->validateLendingDates($loan, $operation);

        return ['previous_lending_id' => $loan->id];
    }

    /**
     * Extracts loan data from a request and validates them.
     * 
     * @param FinancialOperation $operation
     * the operation associated with the lending
     * @param CreateOperationRequest $request
     * the request from which to extract the data
     * @return array
     * the validated data
     * @throws ValidationException
     */
    private function getValidatedLoanUpsertData(
        FinancialOperation $operation, CreateOperationRequest $request
    ) {
        $repaymentLending = Lending::findRepayment($operation->id);
        $repayment = ($repaymentLending) ? $repaymentLending->operation : null;

        $this->validateLendingDates($operation, $repayment);

        return ['expected_date_of_return' => $request->validated('expected_date_of_return')];
    }

    /**
     * Ensures that a loan is not repaid earlier than provided.
     * 
     * @param FinancialOperation $loan
     * the loan to consider
     * @param FinancialOperation|null $repayment
     * the repayment to check (if exists)
     * @return void
     * @throws ValidationException
     */
    private function validateLendingDates(FinancialOperation $loan, FinancialOperation|null $repayment)
    {
        if ($loan && $repayment && $repayment->date->lt($loan->date))
            throw ValidationException::withMessages([
                'date' => trans('validation.repayment_invalid_date')
            ]);
    }
}
