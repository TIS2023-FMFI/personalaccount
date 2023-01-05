<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Http\Controllers\Controller;
use App\Models\Lending;
use Exception;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\throwException;


/**
 * Parent class containing general functions useful for both 'create operation' and 'edit operation' controllers.
 */
class GeneralOperationController extends Controller
{

    /**
     * Saves the given file as an operation attachment belonging to the given user.
     *
     * @param $userId
     * @param $file
     * @return string - the path to the saved file
     */
    public function saveAttachment($userId, $file) : string
    {
        $dir = $this->generateAttachmentDirectory($userId);
        return Storage::putFile($dir, $file);
    }

    /**
     * Returns the name of the directory where attachments for the given user should be stored.
     *
     * @param $userId
     * @return string
     */
    private function generateAttachmentDirectory($userId): string
    {
        return "user_$userId/attachments";
    }

    /**
     * Inserts a lending record into the database, related to the operation with the given id. If that operation already
     * has a lending record, the lending is instead updated with new data.
     *
     * @param $request - CreateOperation/UpdateOperation request containing data about the lending
     * @param $id
     * @return void
     */
    protected function upsertLending($request, $id)
    {
        $lending = Lending::updateOrCreate(
            ['id' => $id],
            [
                'expected_date_of_return' => $request->validated('expected_date_of_return'),
                'previous_lending_id' => $request->validated('previous_lending_id'),
            ]
        );
        if (!$lending->exists) throwException(new Exception('The lending wasn\'t created.'));
    }
}
