<?php

declare(strict_types=1);

namespace Domain\Schedule\Data;

use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;

final class ListAppointmentsData extends Data
{
    public function __construct(
        #[Nullable, DateFormat('Y-m-d')]
        public readonly ?string $date = null,
    ) {}
}
