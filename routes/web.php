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

Route::post('/broadcasting/auth', function () {
    \Log::info('broadcasting.auth hit', ['user' => auth()->user()]);
    return response()->json(['auth' => 'placeholder']); // just to test JSON
});




Route::post('/broadcasting/auth-test', function () {
    return response()->json(['status' => 'working']);
});



Route::get('/test-pusher', function () {
    return view('test-pusher');
});

Route::get('/courier-test', function () {
    return view('courier-test');
});
