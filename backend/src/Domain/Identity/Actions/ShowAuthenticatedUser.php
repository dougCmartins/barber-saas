<?php

declare(strict_types=1);

namespace Domain\Identity\Actions;

use Domain\Identity\Data\UserData;
use Domain\Identity\Models\User;

final class ShowAuthenticatedUser
{
    public function handle(): UserData
    {
        /** @var User $user */
        $user = auth()->user();

        return new UserData(
            id: (int) $user->id,
            barbershop_id: empty($user->barbershop_id) ? null : (int) $user->barbershop_id,
            name: $user->name,
            email: $user->email,
            role: $user->role,
        );
    }
}
