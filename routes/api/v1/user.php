<?php

use App\Http\Controllers\V1\UserController;
use App\Http\Middleware\IdempotencyMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/usuario', [UserController::class, 'store'])->middleware([IdempotencyMiddleware::class]);;
