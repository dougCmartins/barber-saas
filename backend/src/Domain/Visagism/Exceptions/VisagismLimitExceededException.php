<?php

declare(strict_types=1);

namespace Domain\Visagism\Exceptions;

use DomainException;

final class VisagismLimitExceededException extends DomainException
{
    public function __construct()
    {
        parent::__construct('You already used your Visagism AI analysis for today.');
    }

    public function getErrorCode(): string
    {
        return 'VISAGISM_LIMIT_EXCEEDED';
    }

    public function getHttpStatus(): int
    {
        return 429;
    }
}
