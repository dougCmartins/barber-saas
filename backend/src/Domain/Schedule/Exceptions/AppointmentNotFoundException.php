<?php

declare(strict_types=1);

namespace Domain\Schedule\Exceptions;

use DomainException;

final class AppointmentNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Appointment not found.');
    }

    public function getErrorCode(): string
    {
        return 'APPOINTMENT_NOT_FOUND';
    }

    public function getHttpStatus(): int
    {
        return 404;
    }
}
