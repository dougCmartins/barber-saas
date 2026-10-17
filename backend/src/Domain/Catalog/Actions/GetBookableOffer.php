<?php

declare(strict_types=1);

namespace Domain\Catalog\Actions;

use Domain\Catalog\Data\BookableOfferData;
use Domain\Catalog\Data\BookableOfferRequestData;
use Domain\Catalog\Exceptions\BookableOfferNotFoundException;
use Domain\Catalog\Models\Professional;
use Domain\Catalog\Models\Service;

final class GetBookableOffer
{
    public function handle(BookableOfferRequestData $data): BookableOfferData
    {
        $service = Service::query()
            ->withoutGlobalScope('tenant')
            ->where('id', $data->service_id)
            ->where('barbershop_id', $data->barbershop_id)
            ->where('active', true)
            ->first();

        $professional = Professional::query()
            ->withoutGlobalScope('tenant')
            ->where('id', $data->professional_id)
            ->where('barbershop_id', $data->barbershop_id)
            ->where('active', true)
            ->first();

        if (! $service || ! $professional) {
            throw new BookableOfferNotFoundException;
        }

        return new BookableOfferData(
            name: $service->name,
            duration_in_minutes: (int) $service->duration_in_minutes,
            price: (float) $service->price,
        );
    }
}
