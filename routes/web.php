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


Route::get('/courier-test', function () {
    $courierId = 12; // replace with the real courier's user_id
    $token = '36|B5RfdmofhNYlxzFFf3Kx41Nf2kbd0QrJynuGmaJc76215adf'; // your real token

    return view('courier-test', compact('courierId', 'token'));
});
