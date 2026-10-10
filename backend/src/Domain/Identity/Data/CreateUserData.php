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
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

final class CreateUserData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, Email, Max(255), Unique('users', 'email')]
        public readonly string $email,
        #[Required, StringType, Min(8)]
        public readonly string $password,
        #[Required, Enum(UserRole::class)]
        public readonly UserRole $role,
        #[Nullable, IntegerType, Min(1)]
        public readonly ?int $barbershop_id,
    ) {}
}
