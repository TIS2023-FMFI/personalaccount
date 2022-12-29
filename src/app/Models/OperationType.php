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
     * Returns whether this operation type represents any kind of lending.
     *
     *  !!! PLACEHOLDER VALUES !!!
     *
     * @return bool
     */
    public function isLending() : bool{
        return in_array($this->name, ['Lending','Pôžička']);
    }
}
