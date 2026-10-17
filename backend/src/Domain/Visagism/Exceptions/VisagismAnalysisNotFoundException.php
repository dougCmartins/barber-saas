<?php

declare(strict_types=1);

namespace Domain\Visagism\Exceptions;

use DomainException;

final class VisagismAnalysisNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Visagism analysis not found.');
    }

    public function getErrorCode(): string
    {
        return 'VISAGISM_ANALYSIS_NOT_FOUND';
    }

    public function getHttpStatus(): int
    {
        return 404;
    }
}
