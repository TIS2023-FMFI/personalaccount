<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\SapReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SapReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether a user can access a SAP report.
     *
     * @param  \App\Models\User  $user
     * the user whose request to authorize
     * @param  \App\Models\SapReport  $report
     * the SAP report the user is attempting to access
     * @return bool
     * true if the user is allowed to perform this operation, false otherwise
     */
    public function view(User $user, SapReport $report)
    {
        return $user->id === $report->account->user_id;
    }

    /**
     * Determine whether a user can create a SAP report.
     *
     * @param  \App\Models\User  $user
     * the user whose request to authorize
     * @param  \App\Models\Account  $account
     * the account under which the user is attempting to create the SAP report
     * @return bool
     * true if the user is allowed to perform this operation, false otherwise
     */
    public function create(User $user, Account $account)
    {
        return $user->id === $account->user_id;
    }

    /**
     * Determine whether a user can delete a SAP report.
     *
     * @param  \App\Models\User  $user
     * the user whose request to authorize
     * @param  \App\Models\SapReport  $report
     * the SAP report the user is attempting to delete
     * @return bool
     * true if the user is allowed to perform this operation, false otherwise
     */
    public function delete(User $user, SapReport $report)
    {
        return $user->id === $report->account->user_id;
    }
}
