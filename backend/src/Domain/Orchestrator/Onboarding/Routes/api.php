<?php

declare(strict_types=1);

use Domain\Orchestrator\Onboarding\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::post('onboarding', [OnboardingController::class, 'store']);
