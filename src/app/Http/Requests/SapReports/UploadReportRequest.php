<?php

namespace App\Http\Requests\SapReports;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UploadReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
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
