<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Onboarding\Data;

use Domain\Identity\Data\UserData;
use Domain\Tenant\Data\BarbershopData;
use Spatie\LaravelData\Data;

final class OnboardingResultData extends Data
{
    public function __construct(
        public readonly BarbershopData $shop,
        public readonly UserData $owner,
    ) {
    }
}
