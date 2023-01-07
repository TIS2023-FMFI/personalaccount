<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
     * Returns a list of operations belonging to this account.
     *
     * @return HasMany
     */
    public function financialOperations()
    {
        return $this->hasMany(FinancialOperation::class);
    }

    /**
     * Get all SAP reports associated with this account.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sapReports()
    {
        return $this->hasMany(SapReport::class);
    }

    /**
     * Get the account's SAP identifier in the form of a string consisting
     * only of alphanumeric characters and dash ('-') symbols.
     *  
     * @return string
     * the transformed SAP identifier
     */
    public function getSanitizedSapId()
    {
        return Str::replace('/', '-', $this->sap_id);
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
        return $this->financialOperations()->whereBetween('date', [$dateFrom, $dateTo]);
    }

    /**
     * Get all SAP reports which are associated with this account and which were
     * uploaded within a specified period.
     * 
     * @param \Illuminate\Support\Carbon $from
     * the date determining the beginning of the period to consider (inclusive)
     * @param \Illuminate\Support\Carbon $to
     * the date determining the end of the period to consider (inclusive)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sapReportsBetween(Carbon $from, Carbon $to)
    {
        return $this->sapReports()->whereBetween('uploaded_on', [$from, $to]);
    }
}
