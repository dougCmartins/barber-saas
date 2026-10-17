<?php

declare(strict_types=1);

namespace Domain\Visagism\Data;

use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class AnalyzeFaceData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255), FromRouteParameter('barbershop_slug')]
        public readonly string $barbershop_slug,
        #[Nullable, StringType, Max(5_000_000)]
        public readonly ?string $image = null,
        #[Nullable, IntegerType, Min(14), Max(100)]
        public readonly ?int $age = null,
        #[Nullable, StringType, In(['straight', 'wavy', 'curly', 'coily'])]
        public readonly ?string $hair_texture = null,
        #[Nullable, StringType, In(['none', 'light', 'medium', 'full'])]
        public readonly ?string $beard_density = null,
    ) {}
}
