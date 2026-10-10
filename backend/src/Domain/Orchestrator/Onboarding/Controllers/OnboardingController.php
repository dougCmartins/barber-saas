<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Onboarding\Controllers;

use Domain\Orchestrator\Onboarding\Actions\ProcessOnboarding;
use Domain\Orchestrator\Onboarding\Data\OnboardingRequestData;
use Domain\Orchestrator\Onboarding\Data\OnboardingResultData;

final class OnboardingController
{
    public function store(OnboardingRequestData $data, ProcessOnboarding $action): OnboardingResultData
    {
        return $action->handle($data);
    }
}
