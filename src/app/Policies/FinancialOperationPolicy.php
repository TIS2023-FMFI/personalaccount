<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\FinancialOperation;
use App\Models\Lending;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FinancialOperationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether a user can access a financial operation.
     *
     * @param  \App\Models\User  $user
     * the user whose request to authorize
     * @param  \App\Models\FinancialOperation  $financialOperation
     * the financial operation the user is attempting to access
     * @return bool
     * true if the user is allowed to perform this operation, false otherwise
     */
    public function view(User $user, FinancialOperation $financialOperation)
    {
        return $user->id === $financialOperation->user()->id;
    }

    /**
     * Determine whether a user can create a financial operation.
     *
     * @param  \App\Models\User  $user
     * the user whose request to authorize
     * @param  \App\Models\Account  $account
     * the account under which the user is attempting to create the operation
     * @return bool
     * true if the user is allowed to perform this operation, false otherwise
     */
    public function create(User $user, Account $account)
    {
        return $user->accounts->contains($account);
    }

    /**
     * Determine whether a user can create a repayment operation.
     *
     * @param  \App\Models\User  $user
     * the user whose request to authorize
     * @param  \App\Models\Lending  $lending
     * the lending for which the user is attempting to create the repayment
     * @return bool
     * true if the user is allowed to perform this operation, false otherwise
     */
    public function createRepayment(User $user, Lending $lending)
    {
        return $user->id === $lending->operation->user()->id;
    }

    /**
     * Determine whether a user can update a financial operation.
     *
     * @param  \App\Models\User  $user
     * the user whose request to authorize
     * @param  \App\Models\FinancialOperation  $financialOperation
     * the financial operation the user is attempting to update
     * @return bool
     * true if the user is allowed to perform this operation, false otherwise
     */
    public function update(User $user, FinancialOperation $financialOperation)
    {
        return $user->id === $financialOperation->user()->id;
    }

    /**
     * Determine whether a user can delete a financial operation.
     *
     * @param  \App\Models\User  $user
     * the user whose request to authorize
     * @param  \App\Models\FinancialOperation  $financialOperation
     * the financial operation the user is attempting to delete
     * @return bool
     * true if the user is allowed to perform this operation, false otherwise
     */
    public function delete(User $user, FinancialOperation $financialOperation)
    {
        //DB::enableQueryLog();
        Log::debug('Policy for deleting financial operation data,
        Financial Op.: {data}
        finOp user: {data2}', ['data' => $financialOperation, 'data2' => $financialOperation->user()]);
        Log::debug(DB::getQueryLog());
        return $user->id === $financialOperation->user()->id;
    }
}
