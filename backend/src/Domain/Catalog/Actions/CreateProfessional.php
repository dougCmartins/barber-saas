<?php

declare(strict_types=1);

namespace Domain\Catalog\Actions;

use Domain\Catalog\Data\CreateProfessionalData;
use Domain\Catalog\Data\ProfessionalData;
use Domain\Catalog\Models\Professional;

final class CreateProfessional
{
    public function handle(CreateProfessionalData $data): ProfessionalData
    {
        $barbershopId = auth()->user()->barbershop_id;

        $professional = Professional::query()->create([
            'barbershop_id' => (int) $barbershopId,
            'user_id' => $data->user_id,
            'name' => $data->name,
            'specialty' => $data->specialty,
            'avatar_url' => $data->avatar_url,
            'experience_years' => $data->experience_years,
        ]);

        $professional->refresh();

        return new ProfessionalData(
            id: (int) $professional->id,
            barbershop_id: (int) $professional->barbershop_id,
            user_id: (int) $professional->user_id,
            name: $professional->name,
            specialty: $professional->specialty,
            avatar_url: $professional->avatar_url,
            experience_years: $professional->experience_years,
            active: $professional->active,
        );
    }
}
