<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AccountManagement\ManageAccountController;
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
    ->middleware(['auth', 'ajax', 'jsonify']);


/**
 * Financial Accounts
 */

Route::middleware('auth')->group(function () {
    // TODO not implemented
    Route::get('/', function () {
        return view('welcome');
    });
});
