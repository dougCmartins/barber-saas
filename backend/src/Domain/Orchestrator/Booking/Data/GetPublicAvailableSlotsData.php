<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Booking\Data;

use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class GetPublicAvailableSlotsData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255), FromRouteParameter('slug')]
        public readonly string $slug,
        #[Required, IntegerType, Min(1), FromRouteParameter('professional_id')]
        public readonly int $professional_id,
        #[Required, DateFormat('Y-m-d')]
        public readonly string $date,
        /** @var list<int> */
        #[Required, ArrayType, Min(1)]
        public readonly array $service_ids,
    ) {}
}
