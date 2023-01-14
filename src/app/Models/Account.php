<?php

namespace App\Models;

use App\Http\Helpers\FileHelper;
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
     * Gets all financial operations belonging to this account.
     *
     * @return HasMany
     */
    public function operations()
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
     * the transformed title
     */
    public function getSanitizedTitle()
    {
        return FileHelper::sanitizeString($this->title);
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
        $incomes = $this->operations()->incomes()->sum('sum');
        $expenses = $this->operations()->expenses()->sum('sum');

        return round($incomes - $expenses, 3);
    }

    /**
     * Builds a query requesting financial operations which belong to this account
     * and whose date is in the specified interval.
     *
     * @param Carbon $from
     * earliest date in the interval
     * @param Carbon $to
     * latest date in the interval
     * @return HasMany the result query
     */
    public function operationsBetween(Carbon $from, Carbon $to)
    {
        return $this->operations()->whereBetween('date', [$from, $to]);
    }

    /**
     * Get all SAP reports which are associated with this account and which were
     * exported or uploaded within a specified period.
     *
     * @param \Illuminate\Support\Carbon $from
     * the date determining the beginning of the period to consider (inclusive)
     * @param \Illuminate\Support\Carbon $to
     * the date determining the end of the period to consider (inclusive)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sapReportsBetween(Carbon $from, Carbon $to)
    {
        return $this->sapReports()->whereBetween('exported_or_uploaded_on', [$from, $to]);
    }
}
