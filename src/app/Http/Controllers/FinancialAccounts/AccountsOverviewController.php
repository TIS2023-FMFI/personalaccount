<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialAccounts\CreateOrUpdateAccountRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Manages the 'financial accounts' view and creation of new financial accounts.
 */
class AccountsOverviewController extends Controller
{
    /**
     * Fills the 'accounts overview' view with a list of accounts belonging to the current user
     *
     * @return Application|Factory|View
     * the view filled with data
     */
    public function show()
    {
        return view('finances.index', [
            'accounts' => Auth::user()->accounts
        ]);
    }

    /**
     * Handles the request to add a new financial account for the current user
     *
     * @param CreateOrUpdateAccountRequest $request
     * the HTTP request to create an operation
     * @return Application|ResponseFactory|Response
     * a response containing information about this operation's result
     */
    public function createFinancialAccount(CreateOrUpdateAccountRequest $request)
    {
        $user = Auth::user();

        $account = $user->accounts()->create([
            'title' => $request->validated('title'),
            'sap_id' => $request->validated('sap_id')
        ]);

        if (! $account->exists)
            return response(trans('financial_accounts.create.failed'), 500);
        return response(trans('financial_accounts.create.success'), 201);
    }
}
