<?php

declare(strict_types=1);

namespace Domain\Visagism\Data;

use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class SaveManualConsultationData extends Data
{
    /**
     * @param  list<string>  $reference_photos
     */
    public function __construct(
        #[Required, StringType, Max(255), FromRouteParameter('barbershop_slug')]
        public readonly string $barbershop_slug,
        #[Required, ArrayType, Min(1), Max(2)]
        public readonly array $reference_photos,
        #[Required, StringType, Max(1000)]
        public readonly string $desired_notes,
        #[Required, StringType, Max(1000)]
        public readonly string $disliked_notes,
    ) {}
}
