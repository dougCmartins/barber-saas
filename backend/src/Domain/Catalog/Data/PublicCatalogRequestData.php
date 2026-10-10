<?php

declare(strict_types=1);

namespace Domain\Catalog\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class PublicCatalogRequestData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $barbershop_slug,
    ) {}
}
