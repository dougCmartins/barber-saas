<?php

declare(strict_types=1);

namespace Domain\Catalog\Exceptions;

use DomainException;

final class BarbershopNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Barbershop not found.');
    }

    public function getErrorCode(): string
    {
        return 'BARBERSHOP_NOT_FOUND';
    }

    public function getHttpStatus(): int
    {
        return 404;
    }
}
