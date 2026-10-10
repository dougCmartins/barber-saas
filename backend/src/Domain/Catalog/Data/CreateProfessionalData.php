<?php

declare(strict_types=1);

namespace Domain\Catalog\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class CreateProfessionalData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $user_id,
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $specialty,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $avatar_url,
        #[Nullable, IntegerType, Min(0)]
        public readonly ?int $experience_years,
    ) {}
}
