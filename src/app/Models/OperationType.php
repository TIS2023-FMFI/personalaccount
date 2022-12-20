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
     * Returns a collection of the operations which have this type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function financialOperations()
    {
        return $this->hasMany(FinancialOperation::class);
    }
}
