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
     * Get the operation with which is this lending associated.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function financialOperation()
    {
        return $this->hasOne(FinancialOperation::class, 'id', 'id');
    }
}
