<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Http\Controllers\Controller;
use App\Models\FinancialOperation;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function PHPUnit\Framework\throwException;

class OperationDetailController extends Controller
{
    /**
     * Returns the "info" view containing information about a single operation.
     *
     * @param $operation_id
     * @return Application|Factory|View
     */
    public function show($operation_id){

        $operation = FinancialOperation::findOrFail($operation_id);
        $lending = $operation->lending;

        return view('finances.modals.operation', [
            'operation' => $operation,
            'lending' => $lending
        ]);

    }

    /**
     * Downloads the attachment file for the given operation.
     *
     * @param $operation_id
     * @return StreamedResponse
     */
    public function downloadAttachment($operation_id){
        $operation = FinancialOperation::findOrFail($operation_id);
        $path = $operation->attachment;
        if (! Storage::exists($path)) throwException(new Exception("The requested file doesn't exist"));
        return Storage::download($path, $this->generateDownloadFileName($path));
    }

    /**
     * Adds a file extension to the given file's name, based on the file's MIME type.
     *
     * @param $path
     * @return string
     */
    public function generateDownloadFileName($path){
        $mime = Storage::mimeType($path);
        $extension = MimeType::search($mime);
        return sprintf('%s.%s',basename($path),$extension);
    }

}
