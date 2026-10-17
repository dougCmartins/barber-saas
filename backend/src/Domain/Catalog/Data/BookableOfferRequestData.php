<?php

declare(strict_types=1);

namespace Domain\Catalog\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class BookableOfferRequestData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $barbershop_id,
        #[Required, IntegerType, Min(1)]
        public readonly int $service_id,
        #[Required, IntegerType, Min(1)]
        public readonly int $professional_id,
    ) {}
}
