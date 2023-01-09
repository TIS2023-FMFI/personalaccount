<?php

namespace App\Http\Requests\FinancialAccounts;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrUpdateAccountRequest extends FormRequest
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
            'title' => ['required', 'max:255'],
            'sap_id' => [
                'required',
                'max:255',
                'regex:/^[A-Z0-9]+([\-\/][A-Z0-9]+)*$/',
            ]
        ];
    }
}
