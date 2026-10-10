<?php

declare(strict_types=1);

namespace Domain\Catalog\Actions;

use Domain\Catalog\Data\ServiceData;
use Domain\Catalog\Models\Service;
use Spatie\LaravelData\DataCollection;

final class ListServices
{
    public function handle(): DataCollection
    {
        $services = Service::query()->get()->map(fn (Service $service): ServiceData => new ServiceData(
            id: (int) $service->id,
            barbershop_id: empty($service->barbershop_id) ? null : (int) $service->barbershop_id,
            name: $service->name,
            description: $service->description,
            price: (float) $service->price,
            duration_in_minutes: (int) $service->duration_in_minutes,
            active: $service->active,
        ));

        return new DataCollection(ServiceData::class, $services);
    }
}
