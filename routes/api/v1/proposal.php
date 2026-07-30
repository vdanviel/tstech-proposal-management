<?php

use App\Http\Controllers\V1\ProposalController;
use App\Http\Middleware\IdempotencyMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/propostas', [ProposalController::class, 'index'])->middleware('auth:sanctum');
Route::post('/propostas', [ProposalController::class, 'store'])->middleware([IdempotencyMiddleware::class, 'auth:sanctum']);
Route::get('/propostas/{id}', [ProposalController::class, 'show'])->middleware('auth:sanctum');
Route::put('/propostas/{id}', [ProposalController::class, 'update'])->middleware([IdempotencyMiddleware::class, 'auth:sanctum']);

Route::post('/propostas/{id}/submit', [ProposalController::class, 'submit'])->middleware([IdempotencyMiddleware::class, 'auth:sanctum']);
Route::post('/propostas/{id}/approve', [ProposalController::class, 'approve'])->middleware([IdempotencyMiddleware::class, 'auth:sanctum']);
Route::post('/propostas/{id}/reject', [ProposalController::class, 'reject'])->middleware([IdempotencyMiddleware::class, 'auth:sanctum']);
Route::post('/propostas/{id}/cancel', [ProposalController::class, 'cancel'])->middleware([IdempotencyMiddleware::class, 'auth:sanctum']);
