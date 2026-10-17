<?php

declare(strict_types=1);

namespace Domain\Schedule\Data;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;

final class ListClientAppointmentsData extends Data
{
    /**
     * @param  list<string>|null  $statuses
     */
    public function __construct(
        #[Nullable, DateFormat('Y-m-d')]
        public readonly ?string $from = null,
        #[Nullable, DateFormat('Y-m-d')]
        public readonly ?string $to = null,
        #[Nullable, ArrayType]
        public readonly ?array $statuses = null,
        #[Nullable, IntegerType, Min(1)]
        public readonly ?int $professional_id = null,
    ) {}
}
