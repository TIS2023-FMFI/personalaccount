<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialOperations\CreateOperationRequest;
use App\Http\Requests\FinancialOperations\EditOperationRequest;
use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\Lending;
use App\Models\OperationType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
/*
    public function downloadAttachment($operation){
        $path = $operation->attachment;
        return Storage::download($operation->attachment, $this->generateDownloadFileName($path));
    }

    public function generateDownloadFileName($path){
        $mime = Storage::mimeType($path);
        $extension = MimeType::search($mime);
        return sprintf('%s.%s',basename($path),$extension);
    }
 */
}
