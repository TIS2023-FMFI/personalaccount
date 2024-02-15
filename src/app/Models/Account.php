<?php

namespace App\Models;

use App\Http\Helpers\FileHelper;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;

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
     * Returns all users who use this account.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'account_user')->withPivot('id', 'account_title');
    }

    /**
     * Returns the authentificated user of this account
     *
     * @return BelongsToMany
     */
    public function user()
    {
        return $this->users()->wherePivot('user_id', Auth::user()->id);
    }

    /**
     * Returns the authentificated user if the user is using this account
     *
     * @return BelongsToMany
     * The retured user,
     * returns null if the user is not using this account
     */
    // public function accountUser()
    // {
    //     return $this->users()->wherePivot('user_id', Auth::user()->id);
    // }

    /**
     * Gets all financial operations belonging to this account.
     *
     * @return HasManyThrough
     */
    public function operations()
    {
        return $this->hasManyThrough(FinancialOperation::class, AccountUser::class);
    }

    /**
     * Gets all SAP operations belonging to this account.
     *
     * @return HasMany
     */
    public function sapOperations()
    {
        return $this->hasMany(SapOperation::class,'operation_type_id');
    }

    /**
     * Get all SAP reports associated with this account.
     *
     * @return HasMany
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
        $incomes = -$this->sapOperations()->where('sum','<',0)->sum('sum');
        $expenses = -$this->sapOperations()->where('sum','>',0)->sum('sum');

        return round($incomes + $expenses, 3);
    }

    /**
     * Builds a query requesting financial operations which belong to this account
     * and whose date is in the specified interval.
     *
     * @param Carbon $from
     * earliest date in the interval
     * @param Carbon $to
     * latest date in the interval
     * @return HasManyThrough the result query
     */
    public function operationsBetween(Carbon $from, Carbon $to)
    {
        return $this->operations()->whereBetween('date', [$from, $to]);
    }

    /**
     * Builds a query requesting financial operations
     * which belong to this account and to the specified user
     * and whose date is in the specified interval.
     *
     * @param User $user
     * specified user
     * @param Carbon $from
     * earliest date in the interval
     * @param Carbon $to
     * latest date in the interval
     * @return HasManyThrough the result query
     */
    public function sapOperationsBetween(Carbon $from, Carbon $to){
        return $this->sapOperations()->where('account_sap_id', $this->sap_id)->whereBetween('date', [$from, $to]);
    }

    public function userOperationsBetween(User $user, Carbon $from, Carbon $to){
        $userId = $user->id;
        $userWithPivot = $this->users()->where('users.id', '=', $userId)->first();
        $accountUserId = $userWithPivot->pivot->id;
        return $this->operations()->where('account_user_id', $accountUserId)->whereBetween('date', [$from, $to]);
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
