<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Onboarding\Data;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

final class OnboardingRequestData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $shop_name,
        #[Required, StringType, Max(255), Unique('barbershops', 'slug')]
        public readonly string $shop_slug,
        #[Required, StringType, Max(255)]
        public readonly string $owner_name,
        #[Required, Email, Max(255), Unique('users', 'email')]
        public readonly string $owner_email,
        #[Required, StringType, Min(8)]
        public readonly string $owner_password,
    ) {
    }
}
