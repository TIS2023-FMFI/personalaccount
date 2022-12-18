<?php

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

Route::get('/', function () {
    return view('finances.index');
});

Route::get('/account', function () {
    return view('finances.account');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/forgot-password', function () {
    return view('auth.forgot_password');
});

Route::get('/first-user', function () {
    return view('auth.first_user');
});

Route::get('/sap-reports', function () {
    return view('finances.sap_reports');
});
