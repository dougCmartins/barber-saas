<?php

declare(strict_types=1);

namespace Domain\Tenant\Actions;

use Domain\Tenant\Data\BarbershopData;
use Domain\Tenant\Data\CreateBarbershopData;
use Domain\Tenant\Models\Barbershop;

final class CreateBarbershop
{
    public function handle(CreateBarbershopData $data): BarbershopData
    {
        $barbershop = Barbershop::query()->create([
            'name' => $data->name,
            'slug' => $data->slug,
        ]);

        $barbershop->refresh();

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
