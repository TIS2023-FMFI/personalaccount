<?php

namespace App\Http\Requests\FinancialAccounts;

use Illuminate\Foundation\Http\FormRequest;

/**
 * A request to create new or update an existing financial account.
 * 
 * Fields: title, sap_id.
 */
class CreateOrUpdateAccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => ['required', 'max:255'],
            'sap_id' => [
                'required',
                'max:255',
                'regex:/^[A-Z0-9]+([\-\/][A-Z0-9]+)*$/',
            ]
        ];
    }
}
