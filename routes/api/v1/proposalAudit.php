<?php

use App\Http\Controllers\V1\ProposalAuditController;
use Illuminate\Support\Facades\Route;

Route::get('/propostas/{id}/auditoria', [ProposalAuditController::class, 'show'])->middleware('auth:sanctum');
