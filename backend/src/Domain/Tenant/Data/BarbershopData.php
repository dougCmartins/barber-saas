<?php

declare(strict_types=1);

namespace Domain\Tenant\Data;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class BarbershopData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $id,
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, StringType, Max(255)]
        public readonly string $slug,
        #[Required, BooleanType]
        public readonly bool $active,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $address,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $city,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $closing_time,
        #[Required, Numeric, Min(0), Max(9.9)]
        public readonly float $rating,
        #[Required, IntegerType, Min(0)]
        public readonly int $total_reviews,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $banner_url,
    ) {}
}
