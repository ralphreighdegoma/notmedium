<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::get('/api/users', function () {
    return response()->json([
        ['id' => 1, 'name' => 'John'],
        ['id' => 2, 'name' => 'Jane'],
    ]);
});

// Catch-all route for SPA - MUST be the last route
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');