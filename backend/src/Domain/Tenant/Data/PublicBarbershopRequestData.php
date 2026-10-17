<?php

declare(strict_types=1);

namespace Domain\Tenant\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class PublicBarbershopRequestData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $slug,
    ) {}
}
