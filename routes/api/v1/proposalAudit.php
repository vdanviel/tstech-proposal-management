<?php

use App\Http\Controllers\V1\ProposalAuditController;
use App\Http\Middleware\IdempotencyMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/propostas/{id}/submit', [ProposalAuditController::class, 'submit'])->middleware([IdempotencyMiddleware::class]);
Route::post('/propostas/{id}/approve', [ProposalAuditController::class, 'approve'])->middleware([IdempotencyMiddleware::class]);
Route::post('/propostas/{id}/reject', [ProposalAuditController::class, 'reject'])->middleware([IdempotencyMiddleware::class]);
Route::post('/propostas/{id}/cancel', [ProposalAuditController::class, 'cancel'])->middleware([IdempotencyMiddleware::class]);

Route::get('/propostas/{id}/auditoria', [ProposalAuditController::class, 'auditoria']);
