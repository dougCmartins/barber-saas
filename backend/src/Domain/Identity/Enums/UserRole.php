<?php

declare(strict_types=1);

namespace Domain\Identity\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Owner = 'owner';
    case Barber = 'barber';
    case Receptionist = 'receptionist';
    case Client = 'client';
}
