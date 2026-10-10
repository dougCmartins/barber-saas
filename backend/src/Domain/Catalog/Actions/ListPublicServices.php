<?php

declare(strict_types=1);

namespace Domain\Catalog\Actions;

use Domain\Catalog\Data\PublicCatalogRequestData;
use Domain\Catalog\Data\ServiceData;
use Domain\Catalog\Exceptions\BarbershopNotFoundException;
use Domain\Catalog\Models\Service;
use Domain\Tenant\Models\Barbershop;
use Spatie\LaravelData\DataCollection;

final class ListPublicServices
{
    public function handle(PublicCatalogRequestData $data): DataCollection
    {
        $barbershop = Barbershop::query()->where('slug', $data->barbershop_slug)->first();

        if (! $barbershop) {
            throw new BarbershopNotFoundException;
        }

        $services = Service::query()
            ->withoutGlobalScope('tenant')
            ->where('barbershop_id', $barbershop->id)
            ->where('active', true)
            ->get()
            ->map(fn (Service $service): ServiceData => new ServiceData(
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
