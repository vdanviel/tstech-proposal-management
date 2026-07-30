<?php

use App\Http\Controllers\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth', [AuthController::class, 'login']);
