<?php

declare(strict_types=1);

use Domain\Tenant\Controllers\PublicBarbershopController;
use Illuminate\Support\Facades\Route;

Route::get('barbershops/{slug}', [PublicBarbershopController::class, 'show']);
