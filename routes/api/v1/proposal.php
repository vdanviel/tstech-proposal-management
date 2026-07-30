<?php

use App\Http\Controllers\V1\ProposalController;
use App\Http\Middleware\IdempotencyMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/propostas', [ProposalController::class, 'index']);
Route::post('/propostas', [ProposalController::class, 'store'])->middleware([IdempotencyMiddleware::class]);
Route::get('/propostas/{id}', [ProposalController::class, 'show']);
Route::patch('/propostas/{id}', [ProposalController::class, 'update'])->middleware([IdempotencyMiddleware::class]);
