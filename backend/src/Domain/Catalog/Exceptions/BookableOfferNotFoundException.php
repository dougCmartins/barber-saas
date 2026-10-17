<?php

declare(strict_types=1);

namespace Domain\Catalog\Exceptions;

use DomainException;

final class BookableOfferNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Service or professional is not available.');
    }

    public function getErrorCode(): string
    {
        return 'BOOKABLE_OFFER_NOT_FOUND';
    }

    public function getHttpStatus(): int
    {
        return 404;
    }
}
