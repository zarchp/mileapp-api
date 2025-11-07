<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class);

Route::middleware(['mock-auth'])->group(function () {
    Route::apiResource('tasks', TaskController::class);
});
