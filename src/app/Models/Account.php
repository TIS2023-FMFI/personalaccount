<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
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
     * Returns the user who owns this account.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns the ID of the user who owns this account.
     *
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user->id;
    }

    /**
     * Returns a list of operations belonging to this account.
     *
     * @return HasMany
     */
    public function financialOperations()
    {
        return $this->hasMany(FinancialOperation::class);
    }

    /**
     * Returns the balance of this account.
     *
     * @return float
     */
    public function getBalance()
    {
        $incomes = $this->financialOperations()->incomes()->sum('sum');
        $expenses = $this->financialOperations()->expenses()->sum('sum');

        return round($incomes - $expenses, 3);
    }


    /**
     * Returns a query for financial operations which belong to this account and their date is in the specified interval.
     *
     * @param $dateFrom - first date in the interval
     * @param $dateTo - last date in the interval
     * @return HasMany
     */
    public function operationsBetween($dateFrom, $dateTo)
    {
        return $this->financialOperations()->whereBetween('date',[$dateFrom, $dateTo]);
    }

}
