<?php

declare(strict_types=1);

namespace Domain\Catalog\Actions;

use Domain\Catalog\Data\CreateServiceData;
use Domain\Catalog\Data\ServiceData;
use Domain\Catalog\Models\Service;

final class CreateService
{
    public function handle(CreateServiceData $data): ServiceData
    {
        $barbershopId = auth()->user()->barbershop_id;

        $service = Service::query()->create([
            'barbershop_id' => empty($barbershopId) ? null : (int) $barbershopId,
            'name' => $data->name,
            'description' => $data->description,
            'price' => $data->price,
            'duration_in_minutes' => $data->duration_in_minutes,
        ]);

        $service->refresh();

        return new ServiceData(
            id: (int) $service->id,
            barbershop_id: empty($service->barbershop_id) ? null : (int) $service->barbershop_id,
            name: $service->name,
            description: $service->description,
            price: (float) $service->price,
            duration_in_minutes: (int) $service->duration_in_minutes,
            active: $service->active,
        );
    }
}
