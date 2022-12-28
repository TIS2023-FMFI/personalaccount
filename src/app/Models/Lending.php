<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public $incrementing = false;

    public function financialOperation(){
        return $this->belongsTo(FinancialOperation::class, 'id', 'id');
    }

    public function getReferencedLending(){
        //
    }
}
