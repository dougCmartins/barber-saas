<?php

declare(strict_types=1);

namespace Domain\Identity\Actions;

use Domain\Identity\Data\CreateUserData;
use Domain\Identity\Data\UserData;
use Domain\Identity\Models\User;
use Illuminate\Support\Facades\Hash;

final class CreateUser
{
    public function handle(CreateUserData $data): UserData
    {
        $user = User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
            'role' => $data->role,
            'barbershop_id' => $data->barbershop_id,
        ]);

        return new UserData(
            id: (int) $user->id,
            barbershop_id: empty($user->barbershop_id) ? null : (int) $user->barbershop_id,
            name: $user->name,
            email: $user->email,
            role: $user->role,
        );
    }
}
