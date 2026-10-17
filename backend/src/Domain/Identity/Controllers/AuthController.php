<?php

declare(strict_types=1);

namespace Domain\Identity\Controllers;

use Domain\Identity\Actions\AuthenticateUser;
use Domain\Identity\Actions\ShowAuthenticatedUser;
use Domain\Identity\Data\LoginRequestData;
use Domain\Identity\Data\TokenData;
use Domain\Identity\Data\UserData;

final class AuthController
{
    public function login(LoginRequestData $data, AuthenticateUser $action): TokenData
    {
        return $action->handle($data);
    }

    public function me(ShowAuthenticatedUser $action): UserData
    {
        return $action->handle();
    }
}
