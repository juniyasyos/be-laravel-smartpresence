<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    if (config('iam.enabled', false)) {
        return redirect()->route('iam.sso.login');
    }

    return response()->json([
        'message' => 'Unauthenticated. Please use POST /api/login for local authentication or enable SSO.'
    ], 401);
})->name('login');
