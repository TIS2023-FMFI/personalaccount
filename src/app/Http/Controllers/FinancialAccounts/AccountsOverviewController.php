<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Models\Account;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AccountsOverviewController extends Controller
{
    /**
     * Fills the 'accounts overview' view with a list of accounts.
     * If the user is an admin, all accounts are shown. Otherwise, only user's accounts are shown.
     *
     * @return Application|Factory|View
     */
    public function show()
    {
        // Check if the authenticated user is an admin
        $accounts = Auth::user()->is_admin ? Account::all() : Auth::user()->accounts;

        // Return the view with accounts data
        return view('finances.index', ['accounts' => $accounts]);
    }
}
