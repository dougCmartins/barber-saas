<?php

declare(strict_types=1);

namespace Domain\Catalog\Controllers;

use Domain\Catalog\Actions\ListPublicProfessionals;
use Domain\Catalog\Actions\ListPublicServices;
use Domain\Catalog\Data\PublicCatalogRequestData;
use Spatie\LaravelData\DataCollection;

final class PublicCatalogController
{
    public function services(string $barbershop_slug, ListPublicServices $action): DataCollection
    {
        return $action->handle(PublicCatalogRequestData::from(
            PublicCatalogRequestData::validate([
                'barbershop_slug' => $barbershop_slug,
            ])
        ));
    }

    public function professionals(string $barbershop_slug, ListPublicProfessionals $action): DataCollection
    {
        return $action->handle(PublicCatalogRequestData::from(
            PublicCatalogRequestData::validate([
                'barbershop_slug' => $barbershop_slug,
            ])
        ));
    }
}
