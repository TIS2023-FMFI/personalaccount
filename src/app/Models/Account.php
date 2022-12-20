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
     * Calculates the balance and sets the balance variable.
     *
     * @return void
     */
    public function calculateBalance()
    {
        $balance = 0.0;
        foreach ($this->financialOperations() as $operation){
            $sum = $operation->sum();
            if ($operation->isExpense()) $sum *= -1;
            $balance += $sum;
        }
        $this->balance = $balance;
    }

    /**
     * Returns the balance
     *
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * Returns whether the balance of this account is less than zero
     *
     * @return bool
     */
    public function hasNegativeBalance(): bool
    {
        return $this->balance < 0.0;
    }
}
