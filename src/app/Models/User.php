<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

/**
 * A representation of an instance in the "users" table.
 */
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
     * Set the user's password.
     * 
     * Note: This method also clears the password_change_required flag.
     * 
     * @param mixed $password
     * the plain-text password that should be set as the new password
     * @return bool
     * true on success, false otherwise
     */
    public function setPassword($password)
    {
        $this->password = Hash::make($password);
        $this->password_change_required = false;

        return $this->save();
    }
}
