<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/courier-test', function (Request $request) {
    $user = \App\Models\User::find(12); // hardcode for testing, or fetch via token
    Auth::login($user); // temporary login for web test

    return view('courier-test');
});


use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);
