<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Http\Controllers\Controller;
use App\Models\FinancialOperation;
use Exception;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function PHPUnit\Framework\throwException;

/**
 * Manages the functionality of the 'operation detail' modal.
 */
class OperationDetailController extends Controller
{
    /**
     * Returns data of a single operation.
     *
     * @param FinancialOperation $operation
     * @return array
     */
    public function getOperationData(FinancialOperation $operation)
    {
        $this->authorize('view', $operation);
        
        return ['operation' => $operation];
    }

    /**
     * Downloads the attachment file for the given operation.
     *
     * @param FinancialOperation $operation
     * @return StreamedResponse
     */
    public function downloadAttachment(FinancialOperation $operation)
    {
        $this->authorize('view', $operation);
        
        $path = $operation->attachment;
        if (! Storage::exists($path)) throwException(new Exception('The requested file doesn\'t exist'));
        return Storage::download($path, $this->generateDownloadFileName($operation));
    }

    /**
     * Generates a name for operation's attachment file, based on the operation's title and the attachment's MIME type.
     *
     * @param $operation
     * @return string
     */
    private function generateDownloadFileName($operation)
    {
        $mime = Storage::mimeType($operation->attachment);
        $extension = MimeType::search($mime);
        $name = $this->removeSpecialCharacters($operation->title);
        return "attachment_$name.$extension";
    }

}
