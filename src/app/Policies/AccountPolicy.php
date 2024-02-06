<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;


    /**
     * Determine whether a user can access an account.
     *
     * @param  \App\Models\User  $user
     * the user whose request to authorize
     * @param  \App\Models\Account  $account
     * the account the user is attempting to access
     * @return bool
     * true if the user is allowed to perform this operation, false otherwise
     */
    public function view(User $user, Account $account)
    {
        return $user->accounts->contains($account);
    }


    /**
     * Determine whether a user can update an account.
     *
     * @param  \App\Models\User  $user
     * the user whose request to authorize
     * @param  \App\Models\Account  $account
     * the account the user is attempting to update
     * @return bool
     * true if the user is allowed to perform this operation, false otherwise
     */
    public function update(User $user, Account $account)
    {
        return $user->accounts->contains($account);
    }


    /**
     * Determine whether a user can delete an account.
     *
     * @param  \App\Models\User  $user
     * the user whose request to authorize
     * @param  \App\Models\Account  $account
     * the account the user is attempting to delete
     * @return bool
     * true if the user is allowed to perform this operation, false otherwise
     */
    public function delete(User $user, Account $account)
    {
        return $user->accounts->contains($account);
    }
}
