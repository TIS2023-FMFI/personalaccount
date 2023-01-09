<?php

namespace App\Http\Requests\FinancialOperations;

use Illuminate\Foundation\Http\FormRequest;

/**
 * A request to change the checked state of a financial operation.
 * 
 * Fields: checked.
 */
class CheckOrUncheckOperationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'checked' => ['required', 'boolean']
        ];
    }
}
