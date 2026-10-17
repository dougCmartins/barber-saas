<?php

declare(strict_types=1);

namespace Domain\Visagism\Data;

use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;

final class VisagismExistingStateData extends Data
{
    public function __construct(
        #[Nullable]
        public readonly ?VisagismActiveRecordData $active = null,
        #[Nullable]
        public readonly ?VisagismTrashedSummaryData $trashed_ai = null,
        #[Nullable]
        public readonly ?VisagismTrashedSummaryData $trashed_manual = null,
    ) {}
}
