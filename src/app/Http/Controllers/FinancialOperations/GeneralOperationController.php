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
     * path to the saved file, or an empty string if the request doesn't contain a file
     */
    protected function saveAttachmentFileFromRequest(Account $account, CreateOrUpdateOperationRequest $request)
    {
        $file = $request->file('attachment');
        if ($file)
            return $this->saveAttachment($account->user, $file);
        return '';
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
     * @param int $operationId
     * ID of the operation associated with the lending
     * @param CreateOrUpdateOperationRequest $request
     * request containing data about the lending
     * @return void
     * @throws DatabaseException
     */
    protected function upsertLending(int $operationId, CreateOrUpdateOperationRequest $request)
    {
        $lending = Lending::updateOrCreate(
            ['id' => $operationId],
            [
                'expected_date_of_return' => $request->validated('expected_date_of_return'),
                'previous_lending_id' => $request->validated('previous_lending_id'),
            ]
        );
        if (!$lending->exists)
            throw new DatabaseException('The lending wasn\'t created.');
    }
}
