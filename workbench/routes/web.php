<?php

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', function (): View {
    return view('home');
});

Route::get('/test', function () {
    return 'Hello Mundo!';
});
