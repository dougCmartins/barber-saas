<?php

declare(strict_types=1);

namespace Domain\Identity\Actions;

use Domain\Identity\Data\LoginRequestData;
use Domain\Identity\Data\TokenData;
use Domain\Identity\Exceptions\InvalidCredentialsException;
use Domain\Identity\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

final class AuthenticateUser
{
    public function handle(LoginRequestData $data): TokenData
    {
        $user = User::query()->where('email', $data->email)->first();

        if (! $user || ! Hash::check($data->password, $user->password)) {
            Log::warning('Authentication failed', [
                'email' => $data->email,
            ]);

            throw new InvalidCredentialsException;
        }

        $token = $user->createToken('auth_token');

        Log::info('User authenticated', [
            'user_id' => $user->id,
        ]);

        return new TokenData(
            access_token: (string) $token->accessToken,
            expires_in: (int) $token->expiresIn,
            token_type: $token->tokenType ?: 'Bearer',
        );
    }
}
