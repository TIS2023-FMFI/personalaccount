<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Http\Controllers\Controller;
use App\Models\Lending;
use Exception;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\throwException;


/**
 * Parent class containing general functions useful for multiple controllers.
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
        $filename = $this->generateAttachmentName($dir);
        Storage::putFileAs($dir, $file, $filename);
        return sprintf('%s/%s', $dir, $filename);
    }

    /**
     * Returns the name of the directory where attachments for the given user should be stored.
     *
     * @param $userId
     * @return string
     */
    public function generateAttachmentDirectory($userId): string
    {
        return sprintf('user_%d/attachments', $userId);
    }

    /**
     * Returns the name for an attachment file in the form "attachment_{id}", where {id} is the smallest number
     * not yet taken by any file in the directory.
     *
     * @param $directory
     * @return string
     */
    public function generateAttachmentName($directory): string
    {
        $num = 0;
        while(true){
            $name = sprintf('attachment_%04d', $num);
            if (!Storage::exists($directory.'/'.$name)) return $name;
            $num++;
        }
    }

    /**
     * Deletes the file at the given path, if the file exists.
     *
     * @param $path
     * @return void
     */
    protected function deleteFileIfExists($path){
        if ($path && Storage::exists($path)) Storage::delete($path);
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
        if (!$lending->exists) throwException(new Exception("The lending wasn't created."));
    }
}
