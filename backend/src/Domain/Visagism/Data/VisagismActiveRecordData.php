<?php

declare(strict_types=1);

namespace Domain\Visagism\Data;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class VisagismActiveRecordData extends Data
{
    public function __construct(
        #[Required]
        public readonly int $id,
        #[Required, StringType, In(['ai', 'manual'])]
        public readonly string $consultation_type,
        #[Nullable]
        public readonly ?VisagismResultData $result = null,
        #[Nullable]
        public readonly ?ManualConsultationData $manual = null,
    ) {}
}
