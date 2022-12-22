<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FinancialOperation extends Model
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
    protected $with = ['operationType'];

    public function scopeExpenses(Builder $query): Builder
    {
        return $query
            ->join('operation_types', 'operation_type_id','=','operation_types.id')
            ->where('expense', '=', true);
    }

    public function scopeIncomes(Builder $query): Builder
    {
        return $query
            ->join('operation_types', 'operation_type_id','=','operation_types.id')
            ->where('expense', '=', false);
    }

    /**
     * Returns the account to which this operation belongs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Returns the type of this operation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function operationType()
    {
        return $this->belongsTo(OperationType::class);
    }

    /**
     * Returns whether this operation is an expense (whether it represents a negative sum).
     *
     * @return bool
     */
    public function isExpense(): bool
    {
        return $this->operationType->expense;
    }
}
