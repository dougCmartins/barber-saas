<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Booking\Controllers;

use Domain\Orchestrator\Booking\Actions\GetPublicAvailableSlots;
use Domain\Orchestrator\Booking\Data\GetPublicAvailableSlotsData;
use Spatie\LaravelData\DataCollection;

final class GetPublicAvailableSlotsController
{
    public function index(
        string $slug,
        string $professional_id,
        GetPublicAvailableSlotsData $data,
        GetPublicAvailableSlots $action,
    ): DataCollection {
        return $action->handle(GetPublicAvailableSlotsData::from(
            GetPublicAvailableSlotsData::validate([
                'slug' => $slug,
                'professional_id' => $professional_id,
                'date' => $data->date,
                'service_ids' => $data->service_ids,
            ])
        ));
    }
}
