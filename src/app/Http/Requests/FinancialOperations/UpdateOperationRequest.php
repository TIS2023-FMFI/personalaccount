<?php

namespace App\Http\Requests\FinancialOperations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

/**
 * A request to create new or update an existing financial operation.
 * 
 * Fields: title, date, operation_type_id, subject, sum, attachment,
 *         expected_date_of_return, previous_lending_id
 */
class UpdateOperationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => ['nullable', 'max:255'],
            'date' => ['nullable', 'date'],
            'subject' => ['nullable', 'max:255'],
            'sum' => ['nullable', 'numeric', 'min:0'],
            'attachment' => ['nullable', File::types(['txt','pdf'])],
            'checked' => ['nullable', 'boolean'],
            'expected_date_of_return' => ['nullable', 'date'],
        ];
    }
}
