<?php

declare(strict_types=1);

namespace Domain\Identity\Data;

use Domain\Identity\Enums\UserRole;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class UserData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $id,
        #[Nullable, IntegerType, Min(1)]
        public readonly ?int $barbershop_id,
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, Email, Max(255)]
        public readonly string $email,
        #[Required, Enum(UserRole::class)]
        public readonly UserRole $role,
    ) {}
}
