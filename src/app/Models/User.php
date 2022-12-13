<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Indicates if the model should be timestamped, using created_at and updated_at columns.
     * 
     * @var mixed
     */
    public $timestamps = false;


    /**
     * Get all login tokens generated for the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loginTokens()
    {
        return $this->hasMany(LoginToken::class);
    }
    
    /**
     * Get the latest login token generated for the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestLoginToken()
    {
        return $this->hasOne(LoginToken::class)->latestOfMany();
    }
}
