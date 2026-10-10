<?php

declare(strict_types=1);

namespace Domain\Catalog\Actions;

use Domain\Catalog\Data\ProfessionalData;
use Domain\Catalog\Models\Professional;
use Spatie\LaravelData\DataCollection;

final class ListProfessionals
{
    public function handle(): DataCollection
    {
        $professionals = Professional::query()->get()->map(fn (Professional $professional): ProfessionalData => new ProfessionalData(
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
