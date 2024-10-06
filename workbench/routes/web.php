<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', function (): View {
    return view('welcome');
});

Route::get('/test', function () {
    return "Hello Mundo!";
});
