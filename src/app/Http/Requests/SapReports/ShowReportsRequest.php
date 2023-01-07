<?php

namespace App\Http\Requests\SapReports;

use App\Http\Requests\Base\DateRequest;

class ShowReportsRequest extends DateRequest
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
}
