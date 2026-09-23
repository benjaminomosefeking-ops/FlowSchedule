<?php

use App\Bundle\FlowScheduler\UI\Controllers\ShiftController;
use App\Http\Controllers\ApiTokenController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [ApiTokenController::class, 'store'])->name('api.token.store');

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/auth/token', [ApiTokenController::class, 'destroy'])->name('api.token.destroy');

    Route::prefix('scheduling')->group(function () {
    Route::post('/shifts', [ShiftController::class, 'store'])->name('scheduling.shifts.store');
    });
});
