<?php

declare(strict_types=1);

namespace Domain\Schedule\Exceptions;

use DomainException;

final class SlotUnavailableException extends DomainException
{
    public function __construct()
    {
        parent::__construct('This time slot is unavailable.');
    }

    public function getErrorCode(): string
    {
        return 'SLOT_UNAVAILABLE';
    }

    public function getHttpStatus(): int
    {
        return 409;
    }
}
