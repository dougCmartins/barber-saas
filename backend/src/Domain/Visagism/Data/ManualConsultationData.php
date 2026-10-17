<?php

declare(strict_types=1);

namespace Domain\Visagism\Data;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class ManualConsultationData extends Data
{
    /**
     * @param  list<string>  $reference_photos
     */
    public function __construct(
        #[Required]
        public readonly int $id,
        #[Required, StringType, In(['manual'])]
        public readonly string $consultation_type,
        #[Required, ArrayType, Max(2)]
        public readonly array $reference_photos,
        #[Required, StringType, Max(1000)]
        public readonly string $desired_notes,
        #[Required, StringType, Max(1000)]
        public readonly string $disliked_notes,
    ) {}
}
