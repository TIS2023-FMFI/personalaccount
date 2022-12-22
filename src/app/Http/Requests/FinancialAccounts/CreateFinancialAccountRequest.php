<?php

namespace App\Http\Requests\FinancialAccounts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateFinancialAccountRequest extends FormRequest
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
        //note - for the 'max' to work, double quotes are apparently necessary (or the pipe delimiter notation)
        return [
            'title' => ['required', "max:255"],
            'sap_id' => ['required', "max:255"]
        ];
    }
}
