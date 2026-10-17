<?php

declare(strict_types=1);

namespace Domain\Schedule\Data;

use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class GetAvailableSlotsData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $professional_id,
        #[Required, DateFormat('Y-m-d')]
        public readonly string $date,
        #[Required, IntegerType, Min(1)]
        public readonly int $duration_in_minutes,
    ) {}
}
