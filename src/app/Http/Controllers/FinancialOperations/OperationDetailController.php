<?php

namespace App\Http\Controllers\FinancialOperations;

use App\Exceptions\StorageException;
use App\Http\Controllers\Controller;
use App\Models\FinancialOperation;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Manages the functionality of the 'operation detail' modal.
 */
class OperationDetailController extends Controller
{
    /**
     * Gets the model of a single operation.
     *
     * @param FinancialOperation $operation
     * the operation whose data are requested
     * @return array
     * an array containing the operation's model
     */
    public function getOperationData(FinancialOperation $operation)
    {
        return ['operation' => $operation];
    }

    /**
     * Downloads the attachment file of a financial operation.
     *
     * @param FinancialOperation $operation
     * the operation whose attachment should be downloaded
     * @return StreamedResponse
     * output stream containing the attachment file
     * @throws StorageException
     */
    public function downloadAttachment(FinancialOperation $operation)
    {
        $path = $operation->attachment;
        if (! Storage::exists($path))
            throw new StorageException('The requested file doesn\'t exist');
        return Storage::download($path, $operation->generateAttachmentFileName());
    }

}
