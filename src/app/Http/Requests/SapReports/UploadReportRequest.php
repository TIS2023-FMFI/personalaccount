<?php

namespace App\Http\Requests\SapReports;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

/**
 * A request upload a new SAP report.
 * 
 * Fields: account_id, sap_report.
 */
class UploadReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'account_id' => ['required', 'numeric', 'exists:accounts,id'],
            'sap_report' => ['required', File::types(['txt'])],
        ];
    }
}
