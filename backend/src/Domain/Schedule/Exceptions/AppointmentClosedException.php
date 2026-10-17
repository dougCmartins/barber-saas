<?php

declare(strict_types=1);

namespace Domain\Schedule\Exceptions;

use DomainException;

final class AppointmentClosedException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Appointment is already closed.');
    }

    public function getErrorCode(): string
    {
        return 'APPOINTMENT_CLOSED';
    }

    public function getHttpStatus(): int
    {
        return 422;
    }
}
