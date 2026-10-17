<?php

declare(strict_types=1);

namespace Domain\Schedule\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class AppointmentServiceItemData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $service_id,
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, IntegerType, Min(1)]
        public readonly int $duration_in_minutes,
        #[Required, Numeric, Min(0)]
        public readonly float $price,
        #[Required, IntegerType, Min(0)]
        public readonly int $position,
    ) {}
}
