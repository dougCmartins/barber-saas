<?php

declare(strict_types=1);

namespace Domain\Schedule\Data;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class AvailableSlotData extends Data
{
    public function __construct(
        #[Required, StringType]
        public readonly string $starts_at,
    ) {}
}
