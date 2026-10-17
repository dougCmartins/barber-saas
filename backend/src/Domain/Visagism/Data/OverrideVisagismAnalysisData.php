<?php

declare(strict_types=1);

namespace Domain\Visagism\Data;

use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class OverrideVisagismAnalysisData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255), FromRouteParameter('barbershop_slug')]
        public readonly string $barbershop_slug,
        #[Required, StringType, In(['oval', 'square', 'round', 'rectangular', 'inverted_triangle', 'diamond'])]
        public readonly string $face_shape,
        #[Required, StringType, In(['straight', 'wavy', 'curly', 'coily'])]
        public readonly string $hair_type,
        #[Required, StringType, Max(500)]
        public readonly string $feedback_reason,
    ) {}
}
