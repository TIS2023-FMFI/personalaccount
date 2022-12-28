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

    public function deleteFileIfExists($path){
        if ($path && Storage::exists($path)) Storage::delete($path);
    }

    public function upsertLending($request, $id)
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
