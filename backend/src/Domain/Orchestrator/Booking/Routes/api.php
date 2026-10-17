<?php

declare(strict_types=1);

use Domain\Orchestrator\Booking\Controllers\BookPublicAppointmentController;
use Domain\Orchestrator\Booking\Controllers\GetPublicAvailableSlotsController;
use Illuminate\Support\Facades\Route;

Route::get(
    'barbershops/{slug}/professionals/{professional_id}/slots',
    [GetPublicAvailableSlotsController::class, 'index'],
);
Route::post('barbershops/{slug}/appointments', [BookPublicAppointmentController::class, 'store']);
