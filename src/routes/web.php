<?php

use App\Http\Controllers\AccountManagement\ManageAccountController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FinancialAccounts\AccountDetailController;
use App\Http\Controllers\FinancialAccounts\FinancialAccountsOverviewController;
use App\Http\Controllers\FinancialOperations\CreateOperationController;
use App\Http\Controllers\FinancialOperations\EditOperationController;
use App\Http\Controllers\FinancialOperations\OperationDetailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Authentication
 */

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])
        ->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/login/{token}', [LoginController::class, 'loginUsingToken'])
        ->name('login-using-token');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])
        ->name('forgot-password');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendLoginLink'])
        ->middleware(['ajax', 'jsonify']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::post('/register', [RegisterController::class, 'register'])
    ->middleware(['ajax', 'jsonify']);


/**
 * Account Management
 */

Route::post('/change-password', [ManageAccountController::class, 'changePassword'])
    ->middleware(['auth', 'auth.session', 'ajax', 'jsonify']);


/**
 * Financial Accounts
 */

Route::middleware(['auth', 'auth.session'])->group(function () {

    Route::get('/', [FinancialAccountsOverviewController::class, 'show'])
        ->name('accounts_overview');

    Route::post('/account', [FinancialAccountsOverviewController::class, 'createFinancialAccount'])
        ->middleware(['ajax', 'jsonify']);
    Route::get('/account/{account_id}', [AccountDetailController::class, 'show']);

    Route::get('/export/{account_id}', [AccountDetailController::class, 'downloadExport']);

    Route::post('/operation', [CreateOperationController::class, 'handleCreateOperationRequest'])
        ->middleware(['ajax', 'jsonify']);

    Route::get('/operation/{operation_id}', [OperationDetailController::class, 'getOperationData']);
    Route::put('/operation/{operation_id}', [EditOperationController::class, 'handleEditOperationRequest'])
        ->middleware(['ajax', 'jsonify']);
    Route::patch('/operation/{operation_id}', [AccountDetailController::class, 'markOperationAsChecked'])
        ->middleware(['ajax', 'jsonify']);
    Route::delete('/operation/{operation_id}', [AccountDetailController::class, 'deleteOperation'])
        ->middleware(['ajax', 'jsonify']);

    Route::get('/attachment/{operation_id}', [OperationDetailController::class, 'downloadAttachment']);

    Route::get('/sap-reports', function () {
        return view('finances.sap_reports');
    });

});

