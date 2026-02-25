<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/auth/design-demo', 'auth-design-demo');
Route::view('/auth/sessions', 'auth-sessions');
