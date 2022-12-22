<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Returns a list of operations belonging to this account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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
    public function getBalance() : float
    {
        $incomes = $this->financialOperations()->incomes()->sum('sum');
        $expenses = $this->financialOperations()->expenses()->sum('sum');

        return round($incomes - $expenses, 3);
    }

}
