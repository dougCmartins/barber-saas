<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Booking\Actions;

use Domain\Catalog\Actions\GetBookableOffer;
use Domain\Catalog\Data\BookableOfferRequestData;
use Domain\Orchestrator\Booking\Data\GetPublicAvailableSlotsData;
use Domain\Schedule\Actions\GetAvailableSlots;
use Domain\Schedule\Data\GetAvailableSlotsData;
use Domain\Tenant\Actions\GetPublicBarbershop;
use Domain\Tenant\Data\PublicBarbershopRequestData;
use Spatie\LaravelData\DataCollection;

final class GetPublicAvailableSlots
{
    public function __construct(
        private readonly GetPublicBarbershop $getPublicBarbershop,
        private readonly GetBookableOffer $getBookableOffer,
        private readonly GetAvailableSlots $getAvailableSlots,
    ) {}

    public function handle(GetPublicAvailableSlotsData $data): DataCollection
    {
        $shop = $this->getPublicBarbershop->handle(new PublicBarbershopRequestData(
            slug: $data->slug,
        ));

        $duration = 0;
        $seen = [];

        foreach ($data->service_ids as $serviceId) {
            $id = (int) $serviceId;

            if (isset($seen[$id])) {
                continue;
            }

            $seen[$id] = true;
            $offer = $this->getBookableOffer->handle(new BookableOfferRequestData(
                barbershop_id: $shop->id,
                service_id: $id,
                professional_id: $data->professional_id,
            ));
            $duration += $offer->duration_in_minutes;
        }

        return $this->getAvailableSlots->handle(new GetAvailableSlotsData(
            professional_id: $data->professional_id,
            date: $data->date,
            duration_in_minutes: $duration,
        ));
    }
}
