<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationType extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Array of attributes excluded from mass assignation.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * Get the expense repayment type.
     *
     * @return OperationType
     */
    public static function getRepaymentExpense()
    {
        return OperationType::where('expense', '=', true)
                            ->where('repayment', '=', true)
                            ->first();
    }

    /**
     * Get the income repayment type.
     *
     * @return OperationType
     */
    public static function getRepaymentIncome()
    {
        return OperationType::where('expense', '=', false)
                            ->where('repayment', '=', true)
                            ->first();
    }
}
