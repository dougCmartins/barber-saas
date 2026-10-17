<?php

declare(strict_types=1);

namespace Domain\Tenant\Controllers;

use Domain\Tenant\Actions\GetPublicBarbershop;
use Domain\Tenant\Data\BarbershopData;
use Domain\Tenant\Data\PublicBarbershopRequestData;

final class PublicBarbershopController
{
    public function show(string $slug, GetPublicBarbershop $action): BarbershopData
    {
        return $action->handle(PublicBarbershopRequestData::from(
            PublicBarbershopRequestData::validate([
                'slug' => $slug,
            ])
        ));
    }
}
