<?php

declare(strict_types=1);

namespace Domain\Schedule\Data;

use Domain\Schedule\Enums\AppointmentStatus;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class AppointmentData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $id,
        #[Required, IntegerType, Min(1)]
        public readonly int $barbershop_id,
        #[Required, IntegerType, Min(1)]
        public readonly int $professional_id,
        #[Required, IntegerType, Min(1)]
        public readonly int $service_id,
        #[Required, StringType, Max(255)]
        public readonly string $client_name,
        #[Required, Email, Max(255)]
        public readonly string $client_email,
        #[Required, StringType]
        public readonly string $starts_at,
        #[Required, StringType]
        public readonly string $ends_at,
        #[Required, Numeric, Min(0)]
        public readonly float $price,
        #[Required, Enum(AppointmentStatus::class)]
        public readonly AppointmentStatus $status,
        #[Nullable, StringType, Max(2000)]
        public readonly ?string $notes = null,
    ) {}
}
