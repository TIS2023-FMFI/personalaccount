<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialAccounts\CreateFinancialAccountRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FinancialAccountsOverviewController extends Controller
{
    /**
     * Returns the 'index' view filled with a list of accounts belonging to the current user
     *
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
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
     * @param CreateFinancialAccountRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function createFinancialAccount(CreateFinancialAccountRequest $request)
    {

        $user = Auth::user();
        $title = $request->validated('title');
        $sap_id = $request->validated('sap_id');

        $account = $user->accounts()->create([
            'title' => $title,
            'sap_id' => $sap_id
        ]);

        if ($account->exists) return response(trans('finance_accounts.new.success'), 201);
        return response(trans('finance_accounts.new.failed'), 500);
    }

}
