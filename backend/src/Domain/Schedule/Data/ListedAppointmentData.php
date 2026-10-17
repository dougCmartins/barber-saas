<?php

declare(strict_types=1);

namespace Domain\Schedule\Data;

use Domain\Schedule\Enums\AppointmentStatus;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class ListedAppointmentData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $id,
        #[Required, StringType]
        public readonly string $starts_at,
        #[Required, StringType]
        public readonly string $ends_at,
        #[Required, StringType, Max(255)]
        public readonly string $client_name,
        #[Required, StringType, Max(255)]
        public readonly string $service_name,
        #[Required, StringType, Max(255)]
        public readonly string $professional_name,
        #[Required, Numeric, Min(0)]
        public readonly float $price,
        #[Required, Enum(AppointmentStatus::class)]
        public readonly AppointmentStatus $status,
    ) {}
}
