<?php

declare(strict_types=1);

namespace Domain\Catalog\Actions;

use Domain\Catalog\Data\ProfessionalData;
use Domain\Catalog\Data\PublicCatalogRequestData;
use Domain\Catalog\Exceptions\BarbershopNotFoundException;
use Domain\Catalog\Models\Professional;
use Domain\Tenant\Models\Barbershop;
use Spatie\LaravelData\DataCollection;

final class ListPublicProfessionals
{
    public function handle(PublicCatalogRequestData $data): DataCollection
    {
        $barbershop = Barbershop::query()->where('slug', $data->barbershop_slug)->first();

        if (! $barbershop) {
            throw new BarbershopNotFoundException;
        }

        $professionals = Professional::query()
            ->withoutGlobalScope('tenant')
            ->where('barbershop_id', $barbershop->id)
            ->where('active', true)
            ->get()
            ->map(fn (Professional $professional): ProfessionalData => new ProfessionalData(
                id: (int) $professional->id,
                barbershop_id: (int) $professional->barbershop_id,
                user_id: (int) $professional->user_id,
                name: $professional->name,
                specialty: $professional->specialty,
                avatar_url: $professional->avatar_url,
                experience_years: $professional->experience_years,
                active: $professional->active,
            ));

        return new DataCollection(ProfessionalData::class, $professionals);
    }
}
