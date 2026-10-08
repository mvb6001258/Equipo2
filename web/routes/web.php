<?php

use App\Http\Controllers\TraceabilityController;
use Illuminate\Support\Facades\Route;

// Redirect root to traceability index
Route::get('/', [TraceabilityController::class, 'index'])->name('traceability.index');

// Farms and Batches
Route::post('/farms', [TraceabilityController::class, 'storeFarm'])->name('farms.store');
Route::post('/batches', [TraceabilityController::class, 'storeBatch'])->name('batches.store');

// Timeline & Blockchain Traceability
Route::get('/batch/{token}', [TraceabilityController::class, 'show'])->name('traceability.show');
Route::post('/batch/{id}/event', [TraceabilityController::class, 'storeEvent'])->name('traceability.storeEvent');
