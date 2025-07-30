<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});




Route::get('/courier-test', function () {
    return view('courier-test');
})->middleware(['auth']);
