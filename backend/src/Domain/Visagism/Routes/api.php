<?php

declare(strict_types=1);

use Domain\Visagism\Controllers\VisagismController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function (): void {
    Route::get('barbershops/{barbershop_slug}/visagism/existing', [VisagismController::class, 'existing']);
    Route::post('barbershops/{barbershop_slug}/visagism/analyze', [VisagismController::class, 'analyze']);
    Route::post('barbershops/{barbershop_slug}/visagism/override', [VisagismController::class, 'override']);
    Route::post('barbershops/{barbershop_slug}/visagism/manual', [VisagismController::class, 'manual']);
    Route::post('barbershops/{barbershop_slug}/visagism/restore', [VisagismController::class, 'restore']);
    Route::post('barbershops/{barbershop_slug}/visagism/force-delete', [VisagismController::class, 'forceDelete']);
});
