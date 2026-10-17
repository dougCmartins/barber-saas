<?php

declare(strict_types=1);

namespace Domain\Schedule\Data;

use Domain\Schedule\Enums\AppointmentStatus;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class ClientAppointmentData extends Data
{
    /**
     * @param  list<string>  $service_names
     */
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $id,
        #[Required, StringType]
        public readonly string $starts_at,
        #[Required, StringType]
        public readonly string $ends_at,
        #[Required, IntegerType, Min(1)]
        public readonly int $professional_id,
        #[Required, StringType, Max(255)]
        public readonly string $professional_name,
        #[Nullable, StringType, Max(2048)]
        public readonly ?string $professional_avatar_url,
        #[Required, ArrayType]
        public readonly array $service_names,
        #[Required, Numeric, Min(0)]
        public readonly float $price,
        #[Required, Enum(AppointmentStatus::class)]
        public readonly AppointmentStatus $status,
        #[Nullable, StringType, Max(2000)]
        public readonly ?string $notes = null,
    ) {}
}
