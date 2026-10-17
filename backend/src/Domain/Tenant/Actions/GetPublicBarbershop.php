<?php

declare(strict_types=1);

namespace Domain\Tenant\Actions;

use Domain\Tenant\Data\BarbershopData;
use Domain\Tenant\Data\PublicBarbershopRequestData;
use Domain\Tenant\Exceptions\BarbershopNotFoundException;
use Domain\Tenant\Models\Barbershop;

final class GetPublicBarbershop
{
    public function handle(PublicBarbershopRequestData $data): BarbershopData
    {
        $barbershop = Barbershop::query()
            ->where('slug', $data->slug)
            ->where('active', true)
            ->first();

        if (! $barbershop) {
            throw new BarbershopNotFoundException;
        }

        return new BarbershopData(
            id: (int) $barbershop->id,
            name: $barbershop->name,
            slug: $barbershop->slug,
            active: $barbershop->active,
            address: $barbershop->address,
            city: $barbershop->city,
            closing_time: $barbershop->closing_time,
            rating: (float) $barbershop->rating,
            total_reviews: (int) $barbershop->total_reviews,
            banner_url: $barbershop->banner_url,
        );
    }
}
