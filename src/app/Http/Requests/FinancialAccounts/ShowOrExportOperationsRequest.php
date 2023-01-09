<?php

namespace App\Http\Requests\FinancialAccounts;

use App\Http\Requests\Base\DateRequest;

class ShowOrExportOperationsRequest extends DateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $account = $this->route('account');

        return $this->user()->can('view', $account);
    }
}
