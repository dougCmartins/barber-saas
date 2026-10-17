<?php

declare(strict_types=1);

namespace Domain\Visagism\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class HaircutSuggestionData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $id,
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, IntegerType, Min(0), Max(100)]
        public readonly int $match_percentage,
        #[Required, StringType, Max(500)]
        public readonly string $justification,
        #[Nullable, StringType, Max(2048)]
        public readonly ?string $image_url,
        #[Required, IntegerType, Min(1)]
        public readonly int $service_id,
        #[Required, Numeric, Min(0)]
        public readonly float $price,
    ) {}
}
