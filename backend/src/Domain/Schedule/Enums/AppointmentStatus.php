<?php

declare(strict_types=1);

namespace Domain\Schedule\Enums;

enum AppointmentStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Canceled = 'canceled';
    case Completed = 'completed';
}
