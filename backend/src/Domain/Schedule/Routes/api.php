<?php

declare(strict_types=1);

use Domain\Schedule\Controllers\AppointmentController;
use Domain\Schedule\Controllers\ClientAppointmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function (): void {
    Route::get('appointments', [AppointmentController::class, 'index']);
    Route::patch('appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
    Route::patch('appointments/{id}/complete', [AppointmentController::class, 'complete']);

    Route::get('client/appointments', [ClientAppointmentController::class, 'index']);
    Route::patch('client/appointments/{id}/cancel', [ClientAppointmentController::class, 'cancel']);
});
