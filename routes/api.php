<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('for-you/blogs', [BlogController::class, 'forYou']);
Route::get('blogs/{slug}/preview', [BlogController::class, 'preview']);


Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    Route::apiResource('blogs', BlogController::class);
    Route::patch('blogs/{id}/status', [BlogController::class, 'updateStatus']);
});