<?php

declare(strict_types=1);

namespace Domain\Identity\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class TokenData extends Data
{
    public function __construct(
        #[Required, StringType]
        public readonly string $access_token,
        #[Required, IntegerType, Min(1)]
        public readonly int $expires_in,
        #[Required, StringType]
        public readonly string $token_type = 'Bearer',
    ) {}
}
