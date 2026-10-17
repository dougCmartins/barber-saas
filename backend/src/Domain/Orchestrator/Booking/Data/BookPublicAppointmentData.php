<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Booking\Data;

use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class BookPublicAppointmentData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255), FromRouteParameter('slug')]
        public readonly string $slug,
        #[Required, IntegerType, Min(1)]
        public readonly int $professional_id,
        /** @var list<int> */
        #[Required, ArrayType, Min(1)]
        public readonly array $service_ids,
        #[Required, StringType, Max(255)]
        public readonly string $client_name,
        #[Required, Email, Max(255)]
        public readonly string $client_email,
        #[Required, Date]
        public readonly string $starts_at,
        #[Nullable, StringType, Max(2000)]
        public readonly ?string $notes = null,
    ) {}
}
