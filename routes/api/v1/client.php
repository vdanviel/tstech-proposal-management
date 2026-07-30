<?php

use App\Http\Controllers\V1\ClientController;
use App\Http\Middleware\IdempotencyMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/clientes', [ClientController::class, 'store'])->middleware([IdempotencyMiddleware::class]);
Route::get('/clientes/{id}', [ClientController::class, 'show']);
