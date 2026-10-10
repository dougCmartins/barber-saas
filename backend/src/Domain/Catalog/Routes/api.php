<?php

declare(strict_types=1);

use Domain\Catalog\Controllers\ProfessionalController;
use Domain\Catalog\Controllers\PublicCatalogController;
use Domain\Catalog\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('barbershops/{barbershop_slug}/services', [PublicCatalogController::class, 'services']);
Route::get('barbershops/{barbershop_slug}/professionals', [PublicCatalogController::class, 'professionals']);

Route::middleware('auth:api')->group(function (): void {
    Route::get('services', [ServiceController::class, 'index']);
    Route::post('services', [ServiceController::class, 'store']);
    Route::get('professionals', [ProfessionalController::class, 'index']);
    Route::post('professionals', [ProfessionalController::class, 'store']);
});
