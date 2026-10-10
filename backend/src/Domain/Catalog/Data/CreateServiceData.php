<?php

declare(strict_types=1);

namespace Domain\Catalog\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class CreateServiceData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Nullable, StringType]
        public readonly ?string $description,
        #[Required, Numeric, Min(0)]
        public readonly float $price,
        #[Required, IntegerType, Min(5)]
        public readonly int $duration_in_minutes,
    ) {}
}
