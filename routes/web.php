<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/admin', 'admin-dashboard');

Route::view('/admin', 'admin-dashboard');
