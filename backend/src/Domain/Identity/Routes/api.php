<?php

declare(strict_types=1);

use Domain\Identity\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
