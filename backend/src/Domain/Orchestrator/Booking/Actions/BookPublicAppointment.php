<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Booking\Actions;

use Domain\Catalog\Actions\GetBookableOffer;
use Domain\Catalog\Data\BookableOfferRequestData;
use Domain\Orchestrator\Booking\Data\BookPublicAppointmentData;
use Domain\Schedule\Actions\CreateAppointment;
use Domain\Schedule\Data\AppointmentData;
use Domain\Schedule\Data\AppointmentServiceItemData;
use Domain\Schedule\Data\CreateAppointmentData;
use Domain\Tenant\Actions\GetPublicBarbershop;
use Domain\Tenant\Data\PublicBarbershopRequestData;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\DataCollection;

final class BookPublicAppointment
{
    public function __construct(
        private readonly GetPublicBarbershop $getPublicBarbershop,
        private readonly GetBookableOffer $getBookableOffer,
        private readonly CreateAppointment $createAppointment,
    ) {}

    public function handle(BookPublicAppointmentData $data): AppointmentData
    {
        $shop = $this->getPublicBarbershop->handle(new PublicBarbershopRequestData(
            slug: $data->slug,
        ));

        $items = [];
        $duration = 0;
        $price = 0.0;
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

            $items[] = new AppointmentServiceItemData(
                service_id: $id,
                name: $offer->name,
                duration_in_minutes: $offer->duration_in_minutes,
                price: $offer->price,
                position: count($items),
            );
            $duration += $offer->duration_in_minutes;
            $price = round($price + $offer->price, 2);
        }

        $startsAt = Carbon::parse($data->starts_at);

        return $this->createAppointment->handle(new CreateAppointmentData(
            barbershop_id: $shop->id,
            professional_id: $data->professional_id,
            service_id: $items[0]->service_id,
            client_name: $data->client_name,
            client_email: $data->client_email,
            starts_at: $startsAt->format('Y-m-d H:i:s'),
            ends_at: $startsAt->copy()->addMinutes($duration)->format('Y-m-d H:i:s'),
            price: $price,
            services: new DataCollection(AppointmentServiceItemData::class, $items),
            notes: $data->notes,
        ));
    }
}
