<?php

namespace App\Http\Requests\FinancialAccounts;

use Illuminate\Foundation\Http\FormRequest;

class FilterOperationsRequest extends FormRequest
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
     * Returns input data from the request.
     * Parent function is overriden to make query paramaters accessible for valdiation.
     *
     * @param $keys
     * @return array
     */
    public function all($keys = null)
    {
        $data = parent::all();

        $data['from'] = $this->query('from');
        $data['to'] = $this->query('to');

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from']
        ];
    }
}
