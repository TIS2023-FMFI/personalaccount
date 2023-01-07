<?php

namespace App\Http\Requests\SapReports;

use App\Http\Requests\Base\DateRequest;

/**
 * A request to show SAP reports filtered by the date they were uploaded.
 * 
 * Fields: from, to.
 */
class ShowReportsRequest extends DateRequest
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
}
