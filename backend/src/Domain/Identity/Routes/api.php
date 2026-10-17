<?php

declare(strict_types=1);

use Domain\Identity\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function (): void {
    Route::get('me', [AuthController::class, 'me']);
});
