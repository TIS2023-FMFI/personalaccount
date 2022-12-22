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
     * Array of related tables which should be eager-loaded from the DB along with this model.
     *
     * @var string[]
     */
    protected $with = ['financialOperations'];

    /**
     * Total sum of the operations for this account.
     *
     * @var float
     */
    private float $balance = 0.0;

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
        $balance = 0.0;
        foreach ($this->financialOperations as $operation){
            $sum = $operation->sum;
            if ($operation->isExpense()) $sum *= -1;
            $balance += $sum;
        }
        return $balance;
    }

    /**
     * Returns whether the balance of this account is less than zero
     *
     * @return bool
     */
    public function hasNegativeBalance(): bool
    {
        return $this->getBalance() < 0.0;
    }
}
