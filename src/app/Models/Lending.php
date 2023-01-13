<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lending extends Model
{
    use hasFactory;

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
    protected $guarded = [];

    /**
     * Whether the DB record for this model should have its ID set automatically according to the incrementing ID rules.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Get the operation with which this lending is associated.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function operation()
    {
        return $this->hasOne(FinancialOperation::class, 'id', 'id');
    }

    /**
     * Find the repayment associated with a loan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    /**
     * Find a repayment associated with a loan.
     *
     * @param int $loanId
     * the id of the loan for which to find a repayment
     * @return Lending|null
     * the repayment or null if none was found
     */
    public static function findRepayment(int $loanId)
    {
        return Lending::where('previous_lending_id', '=', $loanId)->first();
    }

    /**
     * Get the loan with which this lending is associated.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function loan()
    {
        return $this->hasOne(FinancialOperation::class, 'id', 'previous_lending_id');
    }
}
